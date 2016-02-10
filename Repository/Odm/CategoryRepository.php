<?php

namespace PhpInk\Nami\CoreBundle\Repository\Odm;

use PhpInk\Nami\CoreBundle\Model\UserInterface;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\UnitOfWork;
use Doctrine\ODM\MongoDB\Mapping\ClassMetadata;
use Doctrine\ODM\MongoDB\Query\Builder as QueryBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use PhpInk\Nami\CoreBundle\Repository\OdmRepository;
use PhpInk\Nami\CoreBundle\Util\Collection;
use PhpInk\Nami\CoreBundle\Model\Odm\Category;
use PhpInk\Nami\CoreBundle\Model\Odm\User;

class CategoryRepository extends OdmRepository
{
    protected $orderByFields = array(
        'default' => array('path', 'position')
    );

    protected $filterByFields = [
        'id' => 'id',
        'name' => 'name',
        'slug' => 'slug',
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
     * @param int  $id
     * @param User $user
     *
     * @return Category
     */
    public function getItem($id, User $user = null)
    {
        $query = $this->createQueryBuilder('this');
        $query = $this->buildItemsQuery($query, $user);
        // Get category with children
        // ie: WHERE id = 4 OR path LIKE 4,% OR path LIKE %,4,%
        $query
            ->field('id')->equals(intval($id))
            ->addOr($query->expr()->field('id')->equals('/'. intval($id). ',/'))
            ->addOr($query->expr()->field('id')->equals('/,'. intval($id). ',/'))
            ->sort('position')
            ->sort('path');

        $category = $this->buildCategoryTree(
            $query->getQuery()->toArray(), $id
        );
        return $category;
    }

    public function getCategoryTree(User $user = null, $orderBy = array(), $filterBy = array())
    {
        $categories = $this->getItemsQuery(
            $user, $orderBy, $filterBy
        )->getQuery()->toArray();

        return $this->buildCategoryTree($categories);
    }

    public function getMenu()
    {
        $query = $this->getItemsQuery();
        $query->field('pages.active')->equals(true);
        $menu = $query->getQuery()->toArray();
        return $menu;
    }

    public function getCategoryTreePaginated(User $user = null, $orderBy = array(), $filterBy = array())
    {
        return new Collection(
            new ArrayCollection(
                $this->getCategoryTree(
                    $user, $orderBy, $filterBy
                )
            ),
            'nami_api_get_categories'
        );
    }

    /**
     * Build the categories hierarchy tree
     *
     * @param array $categories
     * @return array
     */
    public function buildCategoryTree(array $categories, $rootId = null)
    {
        foreach ($categories as $key => $category) {
            /** @var Category $category */
            if ($category->getParent() &&  (!$rootId || $category->getId() !== $rootId)) {
                $parent = $this->findParentRecursively($categories, $category->getParent());
                if ($parent) {
                    $parent->addItem($category);
                    unset($categories[$key]);
                }

            }
        }
        $categories = array_values($categories);

        if ($rootId && count($categories) === 1) {
            $categories = reset($categories);
        }
        return $categories;
    }

    public function findParentRecursively($categories, $parent)
    {
        $parentFound = null;
        foreach ($categories as $category) {
            /** @var Category $category */
            if ($category->getId() === $parent->getId()) {
                $parentFound = $category;
            } elseif ($category->getItems()) {
                if ($parentFromChild = $this->findParentRecursively(
                    $category->getItems(), $parent)) {
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
            $query = $this->addWhereClause(
                $query, 'active', 'true'
            );
        }
        return $query;
    }

    /**
     * Save sorted categories recursively
     *
     * @param \Iterator $categories
     * @param int|null $parent Parent category
     */
    public function sortCategories($categories, $parent = null)
    {
        $i = 0;
        $em = $this->getDocumentManager();
        $categoryIds = $this->getCategoryIds($categories);
        $dbCategories = $this->findById($categoryIds);
        foreach ($categories as $category) {
            $updatedCategory = null;
            foreach ($dbCategories as $dbCategory) {
                if ($dbCategory->getId() === $category->getId()) {
                    $updatedCategory = $dbCategory;
                }
            }
            // Update category position/parent
            if ($updatedCategory) {
                $updatedCategory->setPosition($i++);
                $updatedCategory->setParent($parent);
                $em->persist($updatedCategory);

            }
            // Save categories recursively on children
            if ($category->getItems() && $updatedCategory) {
                $this->sortCategories(
                    $category->getItems(),
                    $updatedCategory
                );
            }
        }
        $em->flush();
    }

    public function getCategoryIds($categories)
    {
        $ids = array();
        foreach ($categories as $category) {
            $ids[] = $category->getId();
            if ($category->getItems()) {
                $ids = array_merge(
                    $ids, $this->getCategoryIds(
                        $category->getItems()
                    )
                );
            }
        }
        return $ids;
    }

    public function getCategoryRoutes()
    {
        $query = $this
            ->createQueryBuilder('this')
            ->field('level')->equals(0)
            ->select('slug', 'name')
            ->field('active')->equals(true);

        return $query->getQuery()->toArray();

    }

    public function getCategoryFromSlug($slug)
    {
        $query = $this->createQueryBuilder('this')
            ->field('level')->equals(0)
            ->field('slug')->equals($slug);

        $entity = $this->fetchSingleResult($query);
        return $entity;

    }
}
