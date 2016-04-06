<?php

namespace PhpInk\Nami\CoreBundle\Repository\Odm;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\UnitOfWork;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use PhpInk\Nami\CoreBundle\Repository\Odm\AbstractRepository as OdmRepository;
use PhpInk\Nami\CoreBundle\Repository\Core\MenuRepositoryInterface;
use PhpInk\Nami\CoreBundle\Util\Collection;
use PhpInk\Nami\CoreBundle\Model\UserInterface;
use PhpInk\Nami\CoreBundle\Model\MenuLinkInterface;

class MenuRepository extends OdmRepository implements MenuRepositoryInterface
{
    protected $orderByFields = array(
        'default' => array('path', 'position')
    );

    protected $filterByFields = [
        'id' => 'id',
        'name' => 'name',
        'link' => 'link',
        'path' => 'path',
        'parent' => 'parent',
        'level' => 'level',
        'createdAt' => 'createdAt',
        'updatedAt' => 'updatedAt',
    ];

    /**
     * @inheritDoc
     */
    public function __construct(DocumentManager $dm, UnitOfWork $uow, ClassMetadata $class)
    {
        parent::__construct($dm, $uow, $class);
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
        /** @var \Doctrine\ODM\MongoDB\Query\Builder $query */
        $query = $this->createQueryBuilder('this');
        $query = $this->buildItemsQuery($query, $user);
        // Get menuLink with children
        // ie: WHERE id = 4 OR path LIKE 4,% OR path LIKE %,4,%
        $query
            ->field('id')->equals(intval($id))
            ->addOr($query->expr()->field('id')->equals('/'. intval($id). ',/'))
            ->addOr($query->expr()->field('id')->equals('/,'. intval($id). ',/'))
            ->sort('position')
            ->sort('path');

        $menuLink = $this->buildMenuTree(
            $query->getQuery()->toArray(), $id
        );
        return $menuLink;
    }

    public function getMenuTree(UserInterface $user = null, $orderBy = array(), $filterBy = array())
    {
        $menuLinks = $this->getItemsQuery(
            $user, $orderBy, $filterBy
        )->getQuery()->toArray();

        return $this->buildMenuLinkTree($menuLinks);
    }

    public function getMenu()
    {
        /** @var \Doctrine\ODM\MongoDB\Query\Builder $query */
        $query = $this->getItemsQuery();
        $query->field('active')->equals(true);
        $menu = $query->getQuery()->toArray();
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
            'nami_api_get_categories'
        );
    }

    /**
     * Build the categories hierarchy tree
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
    public function applyRoleFiltering($query, UserInterface $user = null)
    {
        if (!$user || !$user->isAdmin()) {
            /** @var \Doctrine\ODM\MongoDB\Query\Builder $query */
            $query = $this->addWhereClause(
                $query, 'active', 'true'
            );
        }
        return $query;
    }

    /**
     * Save sorted categories recursively
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
            // Save categories recursively on children
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
