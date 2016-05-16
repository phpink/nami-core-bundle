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
 * Rest controller for menus
 *
 * @package PhpInk\Nami\CoreBundle\Controller
 * @Annotations\NamePrefix("nami_api_")
 * @author Geoffroy Pierret <geofrwa@yandex.com>
 */
class MenuController extends AbstractController
{
    /**
     * The name of the entity
     * mapped by this controller
     * @var string
     */
    protected $modelName = 'Menu';

    /**
     * List all menus.
     *
     * @ApiDoc(
     *   description = "Get the collection of menu links.",
     *   output = "PhpInk\Nami\CoreBundle\Util\Collection<PhpInk\Nami\CoreBundle\Model\MenuLinkInterface>",
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
     * @param Request               $request      The request object.
     * @param ParamFetcherInterface $paramFetcher Param fetcher service
     *
     * @return array
     */
    public function getMenuAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        /** @var \PhpInk\Nami\CoreBundle\Repository\Core\MenuRepositoryInterface $menuRepo */
        $menuRepo = $this->getRepository('MenuLink');
        $menus = $menuRepo->getMenuTree();
        return $this->restView($menus);
    }

    /**
     * Creates a new menu from the submitted data.
     *
     * @ApiDoc(
     *   description = "Creates a new menu.",
     *   resource = true,
     *   input = "PhpInk\Nami\CoreBundle\Form\Type\MenuType",
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
    public function postMenuAction(Request $request)
    {
        return $this->postItem($request);
    }

    /**
     * Update existing menu from the submitted data
     * or create a new menu at a specific location.
     *
     * @ApiDoc(
     *   description = "Update a menu (or create specific)",
     *   resource = true,
     *   input = "PhpInk\Nami\CoreBundle\Form\Type\MenuType",
     *   statusCodes = {
     *     201 = "Returned when a new resource is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request The request object
     * @param int     $id      The menu id
     *
     * @return FormTypeInterface|View
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException when menu not exist
     */
    public function putMenuAction(Request $request, $id)
    {
        return $this->putItem($request, $id);
    }

    /**
     * Removes a menu.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes={
     *     204="Returned when successful"
     *   }
     * )
     *
     * @param Request $request The request object
     * @param int     $id      The menu id
     *
     * @return mixed
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException when menu not exist
     */
    public function deleteMenuAction(Request $request, $id)
    {
        return $this->deleteItem($request, $id);
    }
}
