<?php

namespace PhpInk\Nami\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\FormTypeInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\RouteRedirectView;
use PhpInk\Nami\CoreBundle\Model\UserInterface;
use PhpInk\Nami\CoreBundle\Model\User;

/**
 * Rest controller for users
 *
 * @package PhpInk\Nami\CoreBundle\Controller
 * @Annotations\NamePrefix("nami_api_")
 * @author Geoffroy Pierret <geofrwa@yandex.com>
 */
class UserController extends AbstractController
{
    /**
     * List all users.
     *
     * @ApiDoc(
     *   description = "Get the collection of users.",
     *   output = "PhpInk\Nami\CoreBundle\Util\PaginatedCollection<PhpInk\Nami\CoreBundle\Model\User>",
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", default="0", description="Offset from which to start listing items.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many items to return.")
     * @Annotations\QueryParam(name="orderBy", array=true, requirements="[a-zA-Z0-9-\.]+", description="Sort by fields")
     * @Annotations\QueryParam(name="filterBy", array=true, requirements="[a-zA-Z0-9-:\.\<\>\!\%+]+", description="Filters")
     *
     * ie: ?offset=2&limit=10&orderBy[createdAt]=0&filterBy[active]=true
     *
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     *
     * @throws AccessDeniedException
     */
    public function getUsersAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        return $this->getAllItems($request, $paramFetcher, 'userFull', 'userStandard');
    }

    /**
     * Creates a new user from the submitted data.
     *
     * @param Request $request The request object
     *
     * @return FormTypeInterface|View
     *
     * @Annotations\Post("/users/register")
     * @ApiDoc(
     *   description = "Creates a new user.",
     *   resource = true,
     *   input = "PhpInk\Nami\CoreBundle\Form\Type\UserType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     */
    public function postUserRegisterAction(Request $request)
    {
        return $this->postItem($request);
    }

    /**
     * Get the logged in user from the token.
     *
     * @return View
     *
     * @throws AccessDeniedException
     *
     * @Annotations\Get("/users/me")
     * @ApiDoc(
     *   description = "Get the current user.",
     *   output = "PhpInk\Nami\CoreBundle\Model\User",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     401 = "Returned when the not logged",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     */
    public function getUserMeAction()
    {
        if (!$user = $this->getLoggedUser()) {
            throw new AccessDeniedException();
        }
        return $this->restView($user, null, 'userFull', 'userStandard');
    }

    /**
     * Get a single user.
     *
     * @param Request $request The request object.
     * @param integer $id      The user id.
     *
     * @return array
     *
     * @throws AccessDeniedException when user is not manager or account owner.
     * @throws NotFoundHttpException when user not exist.
     *
     * @ApiDoc(
     *   description = "Get a single user.",
     *   output = "PhpInk\Nami\CoreBundle\Model\User",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     */
    public function getUserAction(Request $request, $id)
    {
        return $this->getOneItem($request, $id, 'userFull', 'userStandard');
    }

    /**
     * Update existing user from the submitted data
     * or create a new user at a specific location.
     *
     * @param Request $request The request object
     * @param int     $id      The user id
     *
     * @return FormTypeInterface|RouteRedirectView
     *
     * @throws AccessDeniedException when user is not manager or account owner
     * @throws NotFoundHttpException when user not exist
     *
     * @ApiDoc(
     *   description = "Update a user (or create specific)",
     *   resource = true,
     *   input = "PhpInk\Nami\CoreBundle\Form\Type\UserType",
     *   statusCodes = {
     *     201 = "Returned when a new resource is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     */
    public function putUserAction(Request $request, $id)
    {
        return $this->putItem($request, $id);
    }

    /**
     * User Bulk Update.
     *
     * @param Request $request The request object.
     *
     * @return array
     *
     * @ApiDoc(
     *   description = "Update a set of users",
     *   resource = true,
     *   input = "PhpInk\Nami\CoreBundle\Form\Type\BulkType",
     *   statusCodes = {
     *     202 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   },
     *   parameters={
     *     {
     *       "name"="id", "dataType"="array", "required"=true,
     *       "description"="The page ids."
     *     },
     *     {
     *       "name"="fields", "dataType"="array", "required"=true,
     *       "description"="The fields to update if no filter ids."
     *     }
     *   }
     * )
     */
    public function putUsersAction(Request $request)
    {
        return $this->putItems($request);
    }

    /**
     * Removes a user.
     *
     * @param Request $request The request object
     * @param int     $id      The user id
     *
     * @return RouteRedirectView
     *
     * @throws AccessDeniedException when user is not manager or account owner
     * @throws NotFoundHttpException when user not exist
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     */
    public function deleteUserAction(Request $request, $id)
    {
        return $this->deleteItem($request, $id);
    }

    /**
     * User Bulk Delete.
     *
     * @param Request $request The request object.
     *
     * @return array
     *
     * @Annotations\Post("/users/delete")
     * @ApiDoc(
     *   description = "Delete a set of users",
     *   resource = true,
     *   input = "PhpInk\Nami\CoreBundle\Form\Type\BulkType",
     *   statusCodes = {
     *     202 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   },
     *   parameters={
     *     {
     *       "name"="id", "dataType"="array", "required"=true,
     *       "description"="The page ids."
     *     }
     *   }
     * )
     */
    public function postUsersDeleteAction(Request $request)
    {
        return $this->deleteItems($request);
    }

    /**
     * "On Post Registration" hook: Sends a mail
     * with the confirmation token to validate master email
     *
     * @param UserInterface $user    The user saved.
     * @param Request       $request The request object.
     *
     * @return void
     */
    protected function onPostSave($user, Request $request = null)
    {
        /**
         * The mailer service
         * @var $mailer \PhpInk\Nami\CoreBundle\Mailer\MailerInterface
         */
        $mailer = $this->get('nami_api.mailer');
        $mailer->sendConfirmationEmailMessage($user);
    }

    /**
     * {@inheritDoc}
     */
    protected function checkUserAccess($type = 'create', UserInterface $entity = null)
    {
        switch ($type) {
            case 'get_one':
            case 'update':
            case 'delete':
                // If not manager and admin,
                // ALLOW self/edited user
                if (!$this->isAdmin()) {
                    $this->checkIsLoggedUser($entity);

                }
                break;
            case 'get_all':
                $this->checkIsAdmin();
                break;
        }
    }
}
