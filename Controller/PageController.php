<?php

namespace PhpInk\Nami\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use PhpInk\Nami\CoreBundle\Model\ModelInterface;
use PhpInk\Nami\CoreBundle\Util\Analytics;

/**
 * Rest controller for pages
 *
 * @package PhpInk\Nami\CoreBundle\Controller
 * @author  Geoffroy Pierret <geofrwa@yandex.com>
 *
 * @Annotations\NamePrefix("nami_api_")
 */
class PageController extends AbstractController
{
    /**
     * List all pages.
     *
     * @param Request               $request      The request object.
     * @param ParamFetcherInterface $paramFetcher Param fetcher service.
     *
     * @return array
     *
     * @ApiDoc(
     *   description = "Get the collection of pages.",
     *   output = "PhpInk\Nami\CoreBundle\Util\PaginatedCollection<PageInterface>",
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(
     *     name="offset",
     *     requirements="\d+", default="0",
     *     description="Offset from which to start listing items."
     * )
     * @Annotations\QueryParam(
     *     name="limit",
     *     requirements="\d+", default="10",
     *     description="How many items to return."
     * )
     * @Annotations\QueryParam(
     *     name="orderBy", map=true,
     *     requirements="[a-zA-Z0-9-\.]+",
     *     description="Sort by fields"
     * )
     * @Annotations\QueryParam(
     *     name="filterBy", map=true,
     *     requirements="[a-zA-Z0-9-:\.\<\>\!\%+]+",
     *     description="Filters"
     * )
     *
     * ie: ?offset=2&limit=10&orderBy[name]=0&orderBy[page]=en&filterBy[parent]=1
     */
    public function getPagesAction(
        Request $request, ParamFetcherInterface $paramFetcher
    ) {
        return $this->getAllItems($request, $paramFetcher);
    }

    /**
     * Get a single page.
     *
     * @param Request $request The request object.
     * @param int     $id      The page id.
     *
     * @return array
     *
     * @throws NotFoundHttpException when page not exist
     *
     * @ApiDoc(
     *   description = "Get a single page.",
     *   output = "PageInterface",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the page is not found"
     *   }
     * )
     */
    public function getPageAction(Request $request, $id)
    {
        return $this->getOneItem($request, $id);
    }

    /**
     * Get Page from url
     *
     * @param string $slug The page url.
     *
     * @return View
     *
     * @throws NotFoundHttpException when page not exist
     *
     * @Annotations\Get(
     *     "/pages/url/{slug}",
     *     requirements={"slug" = ".+"},
     *     defaults={"slug" = "index"}
     * )
     * @ApiDoc(
     *   description = "Get the Products associated to a specific Tag.",
     *   resource = true,
     *   output = "PageInterface",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the supplier is not found"
     *   }
     * )
     */
    public function getPageFromSlugAction($slug)
    {
        if (empty($slug)) {
            $slug = 'index';
        }
        /**
         * Page repository.
         * @var \PhpInk\Nami\CoreBundle\Repository\PageRepository $repo
         */
        $repo = $this->getRepository();
        $page = $repo->getPageFromSlug($slug);
        if (!$page) {
            $category = $this->getRepository('Category')
                ->getCategoryFromSlug($slug);
            if ($category) {
                $page = $category->generatePage();
            }
        }
        return $this->restView($page);
    }

    /**
     * Creates a new page from the submitted data.
     *
     * @param Request $request The request object
     *
     * @return FormTypeInterface|View
     *
     * @throws AccessDeniedException
     *
     * @ApiDoc(
     *   description = "Creates a new page.",
     *   resource = true,
     *   input = "PhpInk\Nami\CoreBundle\Form\Type\PageType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors",
     *     401 = "Returned when not ROLE_MANAGER OR ROLE_SUPER_ADMIN"
     *   }
     * )
     */
    public function postPagesAction(Request $request)
    {
        return $this->postItem($request);
    }

    /**
     * Update existing page from the submitted data
     * or create a new page at a specific location.
     *
     * @param Request $request The request object
     * @param integer $id      The page id
     *
     * @return FormTypeInterface|View
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException when page not exist
     *
     * @ApiDoc(
     *   description = "Update a page (or create specific)",
     *   resource = true,
     *   input = "PhpInk\Nami\CoreBundle\Form\Type\PageType",
     *   statusCodes = {
     *     201 = "Returned when a new resource is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     */
    public function putPageAction(Request $request, $id)
    {
        return $this->putItem($request, $id);
    }

    /**
     * Page Bulk Update.
     *
     * @param Request $request The request object.
     *
     * @return array
     *
     * @ApiDoc(
     *   description = "Update a set of pages",
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
    public function putPagesAction(Request $request)
    {
        return $this->putItems($request);
    }

    /**
     * Removes a page.
     *
     * @param Request $request The request object.
     * @param int     $id      The page id.
     *
     * @return RouteRedirectView
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException when page not exist
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     */
    public function deletePageAction(Request $request, $id)
    {
        return $this->deleteItem($request, $id);
    }

    /**
     * Page Bulk Delete.
     *
     * @param Request $request The request object.
     *
     * @return array
     *
     * @Annotations\Post("/pages/delete")
     * @ApiDoc(
     *   description = "Delete a set of pages",
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
    public function postPagesDeleteAction(Request $request)
    {
        return $this->deleteItems($request);
    }

    /**
     * Check if the logged in user
     * has edit/delete rights on a page model/collection
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
        parent::checkUserAccess($type, $model);
        // Register a new hit for non-admin users
        if ($type === 'get_one' && !$this->isAdmin()) {
            Analytics::registerPageHit(
                $this->getManager(),
                $this->getRequest()->getClientIp(),
                $this->getRequest()->headers->get('user-agent'),
                $model
            );
        }
    }
}
