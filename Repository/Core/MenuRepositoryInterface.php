<?php

namespace PhpInk\Nami\CoreBundle\Repository\Core;

use PhpInk\Nami\CoreBundle\Repository\RepositoryInterface;
use PhpInk\Nami\CoreBundle\Model\UserInterface;

/**
 * Interface MenuRepositoryInterface
 *
 * @package PhpInk\Nami\CoreBundle\Repository\Odm
 */
interface MenuRepositoryInterface extends RepositoryInterface
{
    public function getMenuTree(UserInterface $user = null, $orderBy = array(), $filterBy = array());

    public function getMenu();

    public function getMenuTreePaginated(UserInterface $user = null, $orderBy = array(), $filterBy = array());

    /**
     * Build the categories hierarchy tree
     *
     * @param array $categories
     * @return array
     */
    public function buildMenuTree(array $categories, $rootId = null);

    public function findParentRecursively($categories, $parent);

    /**
     * Save sorted categories recursively
     *
     * @param \Iterator $categories
     * @param int|null $parent Parent category
     */
    public function sortMenuLinks($categories, $parent = null);

    public function getMenuLinksIds($categories);
}
