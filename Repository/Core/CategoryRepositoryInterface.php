<?php

namespace PhpInk\Nami\CoreBundle\Repository\Core;

use PhpInk\Nami\CoreBundle\Repository\RepositoryInterface;
use PhpInk\Nami\CoreBundle\Model\UserInterface;

/**
 * Interface PageRepositoryInterface
 *
 * @package PhpInk\Nami\CoreBundle\Repository\Odm
 */
interface CategoryRepositoryInterface extends RepositoryInterface
{
    public function getCategoryTree(UserInterface $user = null, $orderBy = array(), $filterBy = array());

    public function getMenu();

    public function getCategoryTreePaginated(UserInterface $user = null, $orderBy = array(), $filterBy = array());

    /**
     * Build the categories hierarchy tree
     *
     * @param array $categories
     * @return array
     */
    public function buildCategoryTree(array $categories, $rootId = null);

    public function findParentRecursively($categories, $parent);

    /**
     * Save sorted categories recursively
     *
     * @param \Iterator $categories
     * @param int|null $parent Parent category
     */
    public function sortCategories($categories, $parent = null);

    public function getCategoryIds($categories);

    public function getCategoryRoutes();

    public function getCategoryFromSlug($slug);
}
