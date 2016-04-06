<?php

namespace PhpInk\Nami\CoreBundle\Repository\Orm;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use PhpInk\Nami\CoreBundle\Repository\Orm\AbstractRepository as OrmRepository;
use PhpInk\Nami\CoreBundle\Repository\Core\MenuRepositoryInterface;
use PhpInk\Nami\CoreBundle\Util\Collection;
use PhpInk\Nami\CoreBundle\Model\MenuLinkInterface;
use PhpInk\Nami\CoreBundle\Model\UserInterface;

class MenuRepository extends OrmRepository implements MenuRepositoryInterface
{
    protected $orderByFields = array(
        'default' => array('this.path', 'this.position')
    );

    protected $filterByFields = array(
        'id' => 'this.id',
        'name' => 'this.name',
        'link' => 'this.link',
        'path' => 'this.path',
        'parent' => 'this.parent',
        'level' => 'this.level',
        'createdAt' => 'this.createdAt',
        'updatedAt' => 'this.updatedAt',
        'createdBy' => 'this.createdBy',
        'updatedBy' => 'this.updatedBy'
    );

    /**
     * @inheritDoc
     */
    public function __construct($em, ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->orderByFields = array_merge(
            $this->orderByFields,
            $this->filterByFields
        );
    }

    /**
     * Item get_one returning an Document
     * checking its accessibility
     *
     * @param int           $id
     * @param UserInterface $user
     *
     * @return MenuLinkInterface
     */
    public function getItem($id, UserInterface $user = null)
    {
        $query = $this->createQueryBuilder('this');
        $query = $this->buildItemsQuery($query, $user);
        // Get menuLink with children
        // ie: WHERE id = 4 OR path LIKE 4,% OR path LIKE %,4,%
        $query
            ->where('this.id = :id')->setParameter('id', intval($id))
            ->addOr($query->expr()->field('id')->equals('/'. intval($id). ',/'))
            ->addOr($query->expr()->field('id')->equals('/,'. intval($id). ',/'))
            ->sort('this.position')
            ->sort('this.path');

        $menuLink = $this->buildMenuLinkTree(
            $query->getQuery()->getResult()->getResult(
                AbstractQuery::HYDRATE_ARRAY
            ), $id
        );
        return $menuLink;
    }

    public function getMenuTree(UserInterface $user = null, $orderBy = array(), $filterBy = array())
    {
        $menuLinks = $this->getItemsQuery(
            $user, $orderBy, $filterBy
        )->getQuery()->getResult();

        return $this->buildMenuTree($menuLinks);
    }

    public function getMenu()
    {
        $query = $this->getItemsQuery();
        $this->addWhereClause($query, 'this.active', true);
        $menu = $query->getQuery()->getResult();
        return $menu;
    }

    public function getMenuTreePaginated(UserInterface $user = null, $orderBy = array(), $filterBy = array())
    {
        return new Collection(
            new ArrayCollection(
                $this->getMenuTree(
                    $user, $orderBy, $filterBy
                )
            ),
            'nami_api_get_menuLinks'
        );
    }

    /**
     * Build the menuLinks hierarchy tree
     *
     * @param array $menuLinks
     * @return array
     */
    public function buildMenuTree(array $menuLinks, $rootId = null)
    {
        foreach ($menuLinks as $key => $menuLink) {
            /** @var MenuLinkInterface $menuLink */
            if ($menuLink->getParent() &&  (!$rootId || $menuLink->getId() !== $rootId)) {
                $parent = $this->findParentRecursively($menuLinks, $menuLink->getParent());
                if ($parent) {
                    $parent->addItem($menuLink);
                    unset($menuLinks[$key]);
                }

            }
        }
        $menuLinks = array_values($menuLinks);

        if ($rootId && count($menuLinks) === 1) {
            $menuLinks = reset($menuLinks);
        }
        return $menuLinks;
    }

    public function findParentRecursively($menuLinks, $parent)
    {
        $parentFound = null;
        foreach ($menuLinks as $menuLink) {
            /** @var MenuLinkInterface $menuLink */
            if ($menuLink->getId() === $parent->getId()) {
                $parentFound = $menuLink;
            } elseif ($menuLink->getItems()) {
                if ($parentFromChild = $this->findParentRecursively(
                    $menuLink->getItems(), $parent)) {
                    $parentFound = $parentFromChild;
                }
            }
            if ($parentFound) { break; }
        }
        return $parentFound;
    }

    /**
     * {@inheritDoc}
     */
    public function applyRoleFiltering($query, UserInterface $user = NULL)
    {
        if (!$user || !$user->isAdmin()) {
            $query = $this->addWhereClause(
                $query, 'this.active', true
            );
        }
        return $query;
    }

    /**
     * Save sorted menuLinks recursively
     *
     * @param \Iterator $menuLinks
     * @param int|null $parent Parent menuLink
     */
    public function sortMenuLinks($menuLinks, $parent = null)
    {
        $i = 0;
        $em = $this->getDocumentManager();
        $menuLinkIds = $this->getMenuLinksIds($menuLinks);
        $dbMenuLinks = $this->findById($menuLinkIds);
        foreach ($menuLinks as $menuLink) {
            $updatedMenuLink = null;
            foreach ($dbMenuLinks as $dbMenuLink) {
                if ($dbMenuLink->getId() === $menuLink->getId()) {
                    $updatedMenuLink = $dbMenuLink;
                }
            }
            // Update menuLink position/parent
            if ($updatedMenuLink) {
                $updatedMenuLink->setPosition($i++);
                $updatedMenuLink->setParent($parent);
                $em->persist($updatedMenuLink);

            }
            // Save menuLinks recursively on children
            if ($menuLink->getItems() && $updatedMenuLink) {
                $this->sortMenuLinks(
                    $menuLink->getItems(),
                    $updatedMenuLink
                );
            }
        }
        $em->flush();
    }

    public function getMenuLinksIds($menuLinks)
    {
        $ids = array();
        foreach ($menuLinks as $menuLink) {
            $ids[] = $menuLink->getId();
            if ($menuLink->getItems()) {
                $ids = array_merge(
                    $ids, $this->getMenuLinksIds(
                        $menuLink->getItems()
                    )
                );
            }
        }
        return $ids;
    }
}
