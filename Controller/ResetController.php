<?php

namespace PhpInk\Nami\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use PhpInk\Nami\CoreBundle\Exception\LogicException;
use PhpInk\Nami\CoreBundle\Form\Type\UserResetType;
use PhpInk\Nami\CoreBundle\Security\UserProvider;
use PhpInk\Nami\CoreBundle\Model\User;

/**
 * Rest controller for login
 *
 * @package PhpInk\Nami\CoreBundle\Controller
 * @Annotations\NamePrefix("nami_api_")
 * @author Geoffroy Pierret <geofrwa@yandex.com>
 */
class ResetController extends AbstractController
{
    /**
     * Request reset user password.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful",
     *     400="Returned when error"
     *   }
     * )
     *
     * @Annotations\Get("/users/reset")
     * @Annotations\QueryParam(name="username", requirements="[a-zA-Z0-9-_@\.]+", description="Username requesting reset.", nullable=false)
     *
     * @param Request $request The request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     * @return View
     *
     * @throws LogicException when user is not confirmed
     * @throws BadRequestHttpException when token is not given
     */
    public function getUserResetAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $data = null;
        $statusCode = Response::HTTP_OK;

        /**
         * Retrieves the user / checks its activation
         * @var $userProvider UserProvider
         */
        $userProvider = $this->get('nami_core.user_provider');
        $usernameOrEmail = $this->getRequiredRequestParam($paramFetcher, 'username');
        $user = $userProvider->findUserByUsernameOrEmail($usernameOrEmail);

        if (!$user) {
            $statusCode = Response::HTTP_BAD_REQUEST;
            $data = array(
                'error' => array(
                    'type' => 'invalid_username',
                    'data' => $usernameOrEmail
                )
            );

        } elseif (!$user->isActive()) {
            $data = array(
                'error' => array(
                    'type' => 'inactive_user',
                    'data' => $usernameOrEmail
                )
            );

        } else {
            if ($user->isPasswordRequestNonExpired(
                $this->container->getParameter('nami_core.reset_token_ttl')
            )) {
                $statusCode = Response::HTTP_BAD_REQUEST;
                $data = [
                    'error' => [
                        'type' => 'password_already_resetted',
                        'data' => $usernameOrEmail
                    ]
                ];

            } else {

                if (!$user->getConfirmationToken()) {
                    /**
                     * Generates the reset token
                     * @var $tokenGenerator
                     * \PhpInk\Nami\CoreBundle\Util\TokenGeneratorInterface
                     */
                    $tokenGenerator = $this->get(
                        'nami_core.util.token_generator'
                    );
                    $user->setConfirmationToken($tokenGenerator->generateToken());

                }

                $user->setPasswordRequestedAt(new \DateTime());
                $this->get('nami_core.user_provider')->updateUser($user);
                /**
                 * Sends the reset email
                 * @var $mailer \PhpInk\Nami\CoreBundle\Mailer\MailerInterface
                 */
                $mailer = $this->get('nami_core.mailer');
                $mailer->sendResettingEmailMessage($user);
                
                $data = array(
                    'username' => $user->getUsername(),
                    'status' => 'reset_mail_sent'
                );
            }
        }
        return View::create($data, $statusCode);
    }

    /**
     * Reset user password
     *
     * @param Request               $request      The request object.
     * @param ParamFetcherInterface $paramFetcher Param fetcher service.
     *
     * @return View
     *
     * @throws LogicException when user is not confirmed
     * @throws BadRequestHttpException when token is not given
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful",
     *     400="Returned when error"
     *   }
     * )
     *
     * @Annotations\Post("/users/reset")
     * @Annotations\QueryParam(
     *   name="token", requirements="[a-zA-Z0-9-_\.]+",
     *   description="Reset token.", nullable=false
     * )
     */
    public function postUserResetAction(
        Request $request, ParamFetcherInterface $paramFetcher
    ) {
        $token = $this->getRequiredRequestParam($paramFetcher, 'token');

        /**
         * User provider service
         * @var $userProvider UserProvider
         */
        $userProvider = $this->get('nami_core.user_provider');
        $user = $userProvider->findUserByConfirmationToken($token);

        $data = null;
        $code = Response::HTTP_BAD_REQUEST;
        if (!$user) {
            $data = [
                'error' => [
                    'type' => 'token_not_found',
                    'data' => $token
                ]
            ];
            $code = Response::HTTP_NOT_FOUND;

        } elseif (!$user->isActive()) {
            $data = [
                'error' => [
                    'type' => 'inactive_user',
                    'data' => $token
                ]
            ];
        } else {


            // Create the form
            $form = $this->createForm(new UserResetType(), $user);
            // Submit the form data
            $form->handleRequest($request);

            // If the submitted data is valid
            if ($form->isValid()) {

                $user->setConfirmationToken(null);
                $user->setPasswordRequestedAt(null);

                $userProvider->updateUser($user);

                // The result is displayed
                $code = Response::HTTP_OK;
                $data = [
                    'username' => $user->getUsername(),
                    'status' => 'resetted'
                ];
            } else {
                // Form errors are displayed
                $data = $form;
            }
        }
        return View::create($data, $code);
    }
}
