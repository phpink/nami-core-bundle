<?php

namespace PhpInk\Nami\CoreBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Form\FormTypeInterface;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use PhpInk\Nami\CoreBundle\Util\Collection;
use PhpInk\Nami\CoreBundle\Util\PaginatedCollection;

/**
 * Rest controller for categories
 *
 * @package PhpInk\Nami\CoreBundle\Controller
 * @Annotations\NamePrefix("nami_api_")
 * @author Geoffroy Pierret <geofrwa@yandex.com>
 */
class CategoryController extends AbstractController
{
    /**
     * List all categories.
     *
     * @ApiDoc(
     *   description = "Get the collection of categories.",
     *   output = "PhpInk\Nami\CoreBundle\Util\Collection<PhpInk\Nami\CoreBundle\Model\Category>",
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     * @Annotations\QueryParam(name="orderBy", map=true, requirements="[a-zA-Z0-9-\.]+", description="Sort by fields")
     * @Annotations\QueryParam(name="filterBy", map=true, requirements="[a-zA-Z0-9-:\.\<\>\!\%+]+", description="Filters")
     *
     * ie: ?offset=2&limit=10&orderBy[name]=0&orderBy[locale]=en&filterBy[parent]=1
     *
     * @param ParamFetcherInterface $paramFetcher Param fetcher service
     *
     * @return array
     */
    public function getCategoriesAction(ParamFetcherInterface $paramFetcher)
    {
        /** @var \PhpInk\Nami\CoreBundle\Repository\Core\CategoryRepositoryInterface $categoryRepo */
        $categoryRepo = $this->getRepository();
        $categories = $categoryRepo->getCategoryTreePaginated(
            $this->getLoggedUser(),
            $paramFetcher->get('orderBy'),
            $paramFetcher->get('filterBy')
        );
        return $this->restView($categories);
    }

    /**
     * List all categories with the associated pages.
     *
     * @ApiDoc(
     *   description = "Get the collection of categories with the associated pages.",
     *   output = "PhpInk\Nami\CoreBundle\Util\Collection<PhpInk\Nami\CoreBundle\Model\Category>",
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     * @Annotations\Get("/categories/menu")
     *
     * @return array
     */
    public function getCategoryMenuAction()
    {
        /** @var \PhpInk\Nami\CoreBundle\Repository\Core\CategoryRepositoryInterface $categoryRepo */
        $categoryRepo = $this->getRepository();
        $menu = $categoryRepo->getMenu();
        return $this->restView($menu);
    }

    /**
     * Get a single category.
     *
     * @ApiDoc(
     *   description = "Get a single category.",
     *   output = "PhpInk\Nami\CoreBundle\Model\Category",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the category is not found"
     *   }
     * )
     *
     *
     * @param Request $request The request object
     * @param int     $id      The category id
     *
     * @return array
     *
     * @throws NotFoundHttpException when category not exist
     */
    public function getCategoryAction(Request $request, $id)
    {
        return $this->getOneItem($request, $id);
    }

    /**
     * Creates a new category from the submitted data.
     *
     * @ApiDoc(
     *   description = "Creates a new category.",
     *   resource = true,
     *   input = "PhpInk\Nami\CoreBundle\Form\Type\CategoryType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors",
     *     401 = "Returned when not ROLE_MANAGER OR ROLE_SUPER_ADMIN"
     *   }
     * )
     *
     * @param Request $request The request object
     *
     * @return FormTypeInterface|View
     *
     * @throws AccessDeniedException
     */
    public function postCategoriesAction(Request $request)
    {
        return $this->postItem($request);
    }

    /**
     * Update existing category from the submitted data
     * or create a new category at a specific location.
     *
     * @ApiDoc(
     *   description = "Update a category (or create specific)",
     *   resource = true,
     *   input = "PhpInk\Nami\CoreBundle\Form\Type\CategoryType",
     *   statusCodes = {
     *     201 = "Returned when a new resource is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request The request object
     * @param int     $id      The category id
     *
     * @return FormTypeInterface|View
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException when category not exist
     */
    public function putCategoryAction(Request $request, $id)
    {
        return $this->putItem($request, $id);
    }

    /**
     * Removes a category.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request The request object
     * @param int     $id      The category id
     *
     * @return RouteRedirectView
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException when category not exist
     */
    public function deleteCategoryAction(Request $request, $id)
    {
        return $this->deleteItem($request, $id);
    }

    /**
     * Sort the categories
     *
     * @Annotations\Post("/categories/sort")
     * @ApiDoc(
     *   description = "Sort Categories position",
     *   output = "PhpInk\Nami\CoreBundle\Util\Collection<PhpInk\Nami\CoreBundle\Model\Category>",
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     * @param Request $request The request object
     * @return View
     */
    public function sortCategoriesAction(Request $request)
    {
        $view = null;
        $this->formType = 'PhpInk\Nami\CoreBundle\Form\Type\Category\SortType';
        $this->checkIsAdmin();

        // Create the FormType
        $formType = $this->createFormType(
            array(
                'isEdit' => true,
                'mapId' => true,
                'user' => $this->getLoggedUser()
            )
        );
        $categories = new Collection(
            new ArrayCollection(),
            'nami_api_get_categories'
        );
        $form = $this->createForm($formType, $categories);

        // Submit the form data
        $form->submit($request);

        // If the submitted data is valid
        if ($form->isValid()) {
            /** @var \PhpInk\Nami\CoreBundle\Repository\Core\CategoryRepositoryInterface $categoryRepo */
            $categoryRepo = $this->getRepository();
            // Form data is saved
            $categoryRepo->sortCategories($categories);

            // The user is redirected to the category list
            $view = $this->routeRedirectView(
                'nami_api_get_categories',
                array(), Codes::HTTP_NO_CONTENT
            );

        } else {
            // Form errors are displayed
            $view = View::create($form, 400);
        }
        return $view;
    }
}
