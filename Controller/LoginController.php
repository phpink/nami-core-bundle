<?php

namespace PhpInk\Nami\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use PhpInk\Nami\CoreBundle\Exception\LogicException;
use PhpInk\Nami\CoreBundle\Security\UserProvider;
use PhpInk\Nami\CoreBundle\Model\User;

/**
 * Rest controller for login
 *
 * @package PhpInk\Nami\CoreBundle\Controller
 * @Annotations\NamePrefix("nami_api_")
 * @author Geoffroy Pierret <geofrwa@yandex.com>
 */
class LoginController extends AbstractController
{
    /**
     * Token authentication
     *
     * @Annotations\Post("/users/token")
     * @ApiDoc(
     *   description = "Get a JWT token",
     *   resource = false,
     *   statusCodes = {
     *     200 = "Returned when login successful",
     *     401 = "Returned when login failed"
     *   },
     *  parameters={
     *      {"name"="username", "dataType"="string", "required"=true, "description"="The username of the account."},
     *      {"name"="password", "dataType"="string", "required"=true, "description"="The password of the account."}
     *  }
     * )
     * @throws AuthenticationCredentialsNotFoundException
     */
    public function postUserTokenAction()
    {
        // The security layer will intercept this request
        return new Response('', 401);
    }

    /**
     * Receive the confirmation token from user email provider
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful",
     *     400="Returned when error"
     *   },
     *   parameters={
     *     {"name"="token", "dataType"="string", "required"=true, "description"="The confirmation token."}
     *   }
     * )
     * @Annotations\QueryParam(name="token", requirements="[a-zA-Z0-9-_\.]+", description="Confirmation token.", nullable=false)
     *
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     * @return View
     *
     * @throws BadRequestHttpException when token is not given
     * @throws NotFoundHttpException   when token is not found
     * @throws LogicException          when user is already active
     */
    public function getUsersConfirmAction(ParamFetcherInterface $paramFetcher)
    {
        /** @var $userProvider UserProvider */
        $userProvider = $this->get('nami_core.user_provider');
        $token = $this->getRequiredRequestParam($paramFetcher, 'token');
        $user = $userProvider->findUserByConfirmationToken($token);

        if (!$user) {
            throw new NotFoundHttpException("The token could not be found");
        } elseif ($user->isActive()) {
            throw new LogicException('User account is already active');
        }
        $user->setConfirmationToken(null);
        $user->setActive(true);
        $userProvider->updateUser($user);

        return View::create(
            array(
                'username' => $user->getUsername(),
                'status' => 'confirmed'
            ),
            Codes::HTTP_OK
        );
    }
}
