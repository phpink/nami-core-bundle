<?php

namespace PhpInk\Nami\CoreBundle\Controller;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception as CoreException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;

use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\Context\Context;
use PhpInk\Nami\CoreBundle\Model\ModelInterface;
use PhpInk\Nami\CoreBundle\Model\UserInterface;
use PhpInk\Nami\CoreBundle\Util\PaginatedCollection;
use PhpInk\Nami\CoreBundle\Exception\ItemInactiveException;

/**
 * Base Rest controller
 *
 * @package PhpInk\Nami\CoreBundle\Controller
 * @author  Geoffroy Pierret <geofrwa@yandex.com>
 */
abstract class AbstractController extends FOSRestController
{
    /**
     * The name of the entity
     * mapped by this controller
     * @var string
     */
    protected $modelName;

    /**
     * The name of the FormType
     * mapped by this controller
     * @var string
     */
    protected $formType;

    /**
     * List all entities.
     *
     * @param Request               $request      The request object.
     * @param ParamFetcherInterface $paramFetcher Param fetcher service.
     * @param string                $fullMode     Serialization mode for managers.
     * @param string                $standardMode Default serialization mode.
     *
     * @return View
     */
    protected function getAllItems(
        Request $request, ParamFetcherInterface $paramFetcher,
        $fullMode = null, $standardMode = null
    ) {
        $this->checkUserAccess('get_all');

        $offset = $paramFetcher->get('offset');
        $offset = is_null($offset) ? 0 : intval($offset);
        $limit = $paramFetcher->get('limit');
        $orderBy = $paramFetcher->get('orderBy');
        $filterBy = $paramFetcher->get('filterBy');

        $dbAdapter = $this->container->getParameter(
            'nami_core.database_adapter'
        );
        $query = $this->getRepository()->getItems(
            $this->getLoggedUser(),
            $offset, $limit, $orderBy, $filterBy
        );
        if ($dbAdapter === 'odm') {
            $count = $query->count();
            $elements = $query->getQuery()->execute();
        } else {
            $paginator = new \Doctrine\ORM\Tools\Pagination\Paginator($query);
            $count = $paginator->count();
            $elements = $paginator->getIterator();
        }


        $collection = new PaginatedCollection(
            $elements,
            $offset, $limit, $count,
            $request->get('_route')
        );
        return $this->restView($collection, null, $fullMode, $standardMode);
    }

    /**
     * Get a single entity.
     *
     * @param Request $request      The request object.
     * @param integer $id           The entity id.
     * @param string  $fullMode     The group name for JMS full display.
     * @param string  $standardMode The group name for JMS std display.
     *
     * @return View
     *
     * @throws NotFoundHttpException when entity not exist
     */
    protected function getOneItem(
        Request $request, $id,
        $fullMode = null, $standardMode = null
    ) {
        $model = $this->getModelById($id);
        if ($model) {
            $this->checkUserAccess('get_one', $model);
        }
        return $this->restView($model, null, $fullMode, $standardMode);
    }

    /**
     * Creates a new entity from the submitted data.
     *
     * @param Request $request         The request object
     *
     * @return View
     */
    protected function postItem(
        Request $request
    ) {
        $this->checkUserAccess('create');
        $model = $this->getRepository()->createModel();
        return $this->processForm(
            $request, $model, [
                'isEdit' => false,
                'isFilter' => false,
            ]
        );
    }

    /**
     * Update existing entity from the submitted data
     * or create a new entity at a specific location.
     *
     * @param Request $request         The request object.
     * @param integer $id              The entity id.
     *
     * @return View
     *
     * @throws NotFoundHttpException when entity not exist
     */
    protected function putItem(
        Request $request, $id
    ) {
        $model = $this->getModelById($id);
        $this->checkUserAccess('update', $model);
        return $this->processForm(
            $request, $model, [
                'isEdit' => true,
                'isFilter' => false,
            ]
        );
    }


    /**
     * Bulk update collection.
     *
     * @param Request $request The request object.
     *
     * @return View
     * @throws ItemInactiveException
     */
    protected function putItems(Request $request)
    {
        $this->checkUserAccess('put_all');
        return $this->processBulkForm(
            $request, $this->getFormType(),
            function (FormType $form, $ids) {

                $update = $this->getRepository()->putItems(
                    $form->getData()['fields'],
                    $ids
                );
                // The result is displayed
                return View::create(
                    array(
                        'update' => $update
                    ),
                    $update ?
                        Response::HTTP_ACCEPTED :
                        Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        );
    }

    /**
     * Removes an entity.
     *
     * @param Request $request The request object
     * @param int     $id      The entity id
     *
     * @return RouteRedirectView
     */
    protected function deleteItem(Request $request, $id)
    {
        $model = $this->getModelById($id);
        $this->checkUserAccess('delete', $model);
        $this->getRepository()->removeModel($model);
        // There is a debate if this should be a 404 or a 204
        // see http://leedavis81.github.io/is-a-http-delete-requests-idempotent/
        $view = $this->routeRedirectView(
            'nami_api_get_'. strtolower($this->getModelName()) . 's',
            array(), Response::HTTP_NO_CONTENT
        );
        return $this->handleView($view);
    }


    /**
     * Bulk update collection.
     *
     * @param Request $request The request object.
     *
     * @return View
     * @throws ItemInactiveException
     */
    protected function deleteItems(Request $request)
    {
        $this->checkUserAccess('delete_all');
        return $this->processBulkForm(
            $request, $this->getFormType(),
            function ($form, $ids) {

                $delete = $this->getRepository()->deleteItems($ids);
                // The result is displayed
                return View::create(
                    array(
                        'delete' => $delete
                    ),
                    $delete ?
                        Response::HTTP_ACCEPTED :
                        Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
        );
    }

    /**
     * Process the bulk form data (ids, optionnal fields when update)
     *
     * @param Request  $request     The request object.
     * @param mixed    $modelType   The fields formtype for the update or false.
     * @param \Closure $onFormValid The function to call when the form is valid
     *
     * @return View
     */
    protected function processBulkForm(
        Request $request, $modelType, \Closure $onFormValid
    ) {
        // Create the form
        $form = $this->createFormFromType(
            'Bulk', null, array(
                'model' => $this->getModelName(),
                'modelType' => $modelType,
                'isEdit' => true,
                'isFilter' => true
            )
        );
        // Submit the form data
        $form->handleRequest($request);
        // If the submitted data is valid
        if ($form->isValid()) {
            // Update items
            $ids = array();
            foreach ($form->getData()['id'] as $model) {
                $ids[] = $model->getId();
            }
            $view = $onFormValid($form, $ids);
        } else {
            // Form errors are displayed
            $view = View::create($form, Response::HTTP_BAD_REQUEST);
        }
        return $view;
    }

    /**
     * Process the POST/PUT submitted data,
     * Creates a form for the entity associated
     * to the controller, that is validated.

     * @param Request        $request         The request object.
     * @param ModelInterface $model           The form entity.
     * @param array          $formTypeOptions The form type options.
     * @param boolean        $triggerHooks    Trigger or not model hooks.
     *
     * @return View
     */
    protected function processForm(
        Request $request, ModelInterface $model,
        $formTypeOptions = array(),
        $triggerHooks = true
    ) {
        $view = null;
        $statusCode = $model->getId() ?
            Response::HTTP_OK : Response::HTTP_CREATED;

        // Create the FormType
        $form = $this->createFormFromType(null, $model, $formTypeOptions);

        // Submit the form data
        $form->handleRequest($request);

        // If the submitted data is valid
        if ($form->isValid()) {
            // Form data is saved
            $model = $this->saveModel($model, $statusCode, $triggerHooks);

            // The result is displayed
            return $this->restView($model, $statusCode);

        } else {
            // Form errors are displayed
            $view = View::create($form->getErrors(), Response::HTTP_BAD_REQUEST);
        }
        return $view;
    }

    /**
     * Creates a new instance of FormType
     * corresponding to the REST Controller
     *
     * @param string $formTypeClass   FormType classname.
     * @param array  $data            Form data.
     * @param array  $formTypeOptions FormType options.
     *
     * @return mixed
     */
    protected function createFormFromType(
        $formTypeClass = null, $data = null, $formTypeOptions = array()
    ) {
        $formTypeClass = $this->getFormType($formTypeClass);
        if (!is_array($formTypeOptions)) {
            $formTypeOptions = array();
        }
        $formTypeOptions['user'] = $this->getLoggedUser();
        $dbAdapter = $this->container->getParameter(
            'nami_core.database_adapter'
        );
        $formTypeOptions['isORM'] = ($dbAdapter === 'orm');
        return $this->createForm($formTypeClass, $data, $formTypeOptions);
    }

    /**
     * Persists an entity after a form submit
     * and calls pre:post controller hooks
     *
     * @param ModelInterface $model        The model to save.
     * @param string         $statusCode   Success HTTP code (200 or 201).
     * @param boolean        $triggerHooks Trigger or not model hooks.
     *
     * @return mixed
     */
    protected function saveModel(
        ModelInterface $model, $statusCode, $triggerHooks = true
    ) {
        if ($statusCode === Response::HTTP_CREATED && $triggerHooks) {
            $this->onPreSave($model);
        } else {
            $this->onPreUpdate($model);
        }

        // The ModelInterface is saved
        $this->persistModel($model);

        if ($statusCode === Response::HTTP_CREATED && $triggerHooks) {
            $this->onPostSave($model);
        } else {
            $this->onPostUpdate($model);
        }
        return $model;
    }

    /**
     * Save/Persist a entity instance
     *
     * @param ModelInterface $model to persist.
     *
     * @return AbstractController
     */
    protected function persistModel(ModelInterface $model)
    {
        $em = $this->getManager();
        $em->persist($model);
        $em->flush();

        return $this;
    }

    /**
     * Display data with JMS serialization &
     * Output it with correct content-type with FOSRest.
     *
     * @param mixed        $data         The data to display.
     * @param integer|null $statusCode   The HTTP status code.
     * @param string       $fullMode     The group name for JMS full view.
     * @param string       $standardMode The group name for JMS std view.
     *
     * @return View
     */
    protected function restView(
        $data, $statusCode = null,
        $fullMode = null, $standardMode = null
    ) {
        // Merge serialization groups with default
        $groups = array(
            $this->isAdmin() ? 'full' : 'standard'
        );
        if ($this->isAdmin() && $fullMode) {
            $groups[] = $fullMode;

        } elseif (!$this->isAdmin() && $standardMode) {
            $groups[] = $standardMode;
        }

        $view = new View($data, $statusCode);

        $view->setContext(
            (new Context())
                ->setSerializeNull(true)
                //->enableMaxDepthChecks()
                ->addGroups(array_merge(array('Default'), $groups))
        );
        return $view;
    }

    /**
     * Get the Doctrine repository
     * corresponding to the controller
     *
     * @param string $name The model name
     * to get the repository for [optional].
     *
     * @return \PhpInk\Nami\CoreBundle\Repository\OdmRepository|
     *         \PhpInk\Nami\CoreBundle\Repository\OrmRepository
     */
    protected function getRepository($name = null)
    {
        $modelName = $name ?
            $name : $this->getModelName();
        if (strpos($modelName, '\\') === false) {
            $modelName = 'NamiCoreBundle:'. $modelName;
        }
        $em = $this->getManager();
        return $em->getRepository($modelName);
    }

    /**
     * Get the Doctrine manager (ODM or ORM)
     *
     * @return mixed
     */
    protected function getManager()
    {
        $em = null;
        if ($this->container->getParameter('nami_core.database_adapter') === 'odm') {
            $em = $this->get('doctrine_mongodb')->getManager();
        } else {
            $em = $this->getDoctrine()->getManager();
        }
        return $em;
    }

    /**
     * Check if the logged in user
     * has edit/delete rights on a model/collection
     *
     * @param string         $type  Access type:
     *                              get_one, get_all, create,
     *                              update, bulk_update or delete.
     * @param ModelInterface $model The model accessed.
     *
     * @return void
     *
     * @throws AccessDeniedException
     * @throws ItemInactiveException
     */
    protected function checkUserAccess(
        $type = 'create', ModelInterface $model = null
    ) {
        if (!$this->isAdmin()) {
            // ONLY if manager or admin
            if (in_array(
                $type,
                array('create', 'update', 'bulk_update', 'delete')
            )) {
                throw new AccessDeniedException('Access denied');

            } elseif ($type === 'get_one'
                && method_exists($model, 'isActive')
                && !$model->isActive()
            ) {
                throw new ItemInactiveException();
            }
        }
    }

    /**
     * Gets a request parameter value from its key
     *
     * @param mixed  $paramFetcher Request Or Param fetcher service.
     * @param string $key          Param key.
     *
     * @return string Parameter value
     *
     * @throws BadRequestHttpException when parameter is empty
     */
    protected function getRequiredRequestParam($paramFetcher, $key)
    {
        $value = $paramFetcher->get($key);
        if (!$value) {
            throw new BadRequestHttpException(
                sprintf('A "%s" parameter is required', $key)
            );
        }
        return $value;
    }

    /**
     * Finds an entity from its ID.
     *
     * @param int    $id             ModelInterface ID
     * @param string $repositoryName Optional repo name
     * if different from the controller-generated one
     *
     * @return ModelInterface
     *
     * @throws NotFoundHttpException when entity not exist
     */
    protected function getModelById($id, $repositoryName = null)
    {
        $model = $this->getRepository($repositoryName)->getItem(
            $id, $this->getLoggedUser()
        );
        if (!$model) {
            throw $this->createNotFoundException(
                sprintf("%s does not exist.", $this->getModelName())
            );
        }
        return $model;
    }

    /**
     * Throws error if the logged in
     * user is not manager or admin
     *
     * @return void
     *
     * @throws AccessDeniedException
     */
    protected function checkIsAdmin()
    {
        if (!$this->isAdmin()) {
            $this->throwAccessDenied();
        }
    }

    /**
     * Get the logged in User.
     *
     * @return UserInterface|null
     */
    protected function getLoggedUser()
    {
        $loggedUser = null;
        try {
            $context = $this->container->get('security.token_storage');
            if ($context && $context->getToken()) {
                $loggedUser = $context->getToken()->getUser();
            }
        } catch (AuthenticationCredentialsNotFoundException $e) {

        }
        return $loggedUser;
    }

    /**
     * Check if the current user has the manager or super admin role
     *
     * @return boolean
     */
    protected function isAdmin()
    {
        $user = $this->getLoggedUser();
        return $user && $user->isAdmin();
    }

    /**
     * Check if the user given is the logged in user
     *
     * @param UserInterface $editedUser The user to compare.
     *
     * @return boolean
     */
    protected function isLoggedUser($editedUser)
    {
        $user = $this->getLoggedUser();
        return $user && $user->getId() === $editedUser->getId();
    }

    /**
     * Throws an error if the user given is not the logged in user
     *
     * @param UserInterface $editedUser The user to compare.
     *
     * @return void
     *
     * @throws AccessDeniedException
     */
    protected function checkIsLoggedUser($editedUser)
    {
        if (!$this->isLoggedUser($editedUser)) {
            $this->throwAccessDenied();
        }
    }

    /**
     * Throws an AccessDeniedException when
     * the user has not the privileges to perform an action
     *
     * @param string $message The message to display when access id denied.
     *
     * @return void
     *
     * @throws AccessDeniedException
     */
    protected function throwAccessDenied($message = null)
    {
        throw new AccessDeniedException($message ? $message : 'Access denied');
    }

    /**
     * Get the cache service instance
     *
     * @return mixed
     */
    protected function getCache()
    {
        return $this->get('beryllium_cache');
    }

    /**
     * Extract the entity class name
     * mapped by the current controller
     *
     * Ex: class ModelInterfaceController > ModelInterface
     *
     * @return string
     */
    protected function getModelName()
    {
        if (is_null($this->modelName)) {
            $this->modelName = str_replace(
                'Controller', '',
                join('', array_slice(explode('\\', get_class($this)), -1))
            );
        }
        return $this->modelName;
    }

    /**
     * Get the entity FormType namespace
     * mapped by the current controller
     *
     * Ex: class ModelInterfaceController >
     *   PhpInk\Nami\CoreBundle\Form\Type\ModelInterfaceType
     *
     * @param string $name FormType name [optionnal]
     *
     * @return string
     */
    protected function getFormType($name = null)
    {
        $formTypeTpl = 'PhpInk\Nami\CoreBundle\Form\Type\%sType';
        if (is_null($name) && is_null($this->formType)) {
            $this->formType = sprintf($formTypeTpl, $this->getModelName());
            $formTypeName = $this->formType;
        } else {
            $formTypeName = sprintf($formTypeTpl, $name);
        }
        return $formTypeName;
    }

    /**
     * PRE-SAVE HOOK
     * To be overridden by REST Controllers
     *
     * @param ModelInterface $model   The model saved.
     * @param Request        $request The request object.
     *
     * @return void
     */
    protected function onPreSave($model, Request $request = null)
    {
    }


    /**
     * PRE-UPDATE HOOK
     * To be overridden by REST Controllers
     *
     * @param ModelInterface $model   The model saved.
     * @param Request        $request The request object.
     *
     * @return void
     */
    protected function onPreUpdate($model, Request $request = null)
    {
    }


    /**
     * POST-SAVE HOOK
     * To be overridden by REST Controllers
     *
     * @param ModelInterface $model   The model saved.
     * @param Request        $request The request object.
     *
     * @return void
     */
    protected function onPostSave($model, Request $request = null)
    {
    }


    /**
     * POST-UPDATE HOOK
     * To be overridden by REST Controllers
     *
     * @param ModelInterface $model   The model saved.
     * @param Request        $request The request object.
     *
     * @return void
     */
    protected function onPostUpdate($model, Request $request = null)
    {
    }
}
