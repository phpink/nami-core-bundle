<?php

namespace PhpInk\Nami\CoreBundle\Repository\Orm;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Tree\Traits\Repository\ORM\MaterializedPathRepositoryTrait;
use PhpInk\Nami\CoreBundle\Repository\Orm\AbstractRepository as OrmRepository;
use PhpInk\Nami\CoreBundle\Repository\Core\CategoryRepositoryInterface;
use PhpInk\Nami\CoreBundle\Util\Collection;
use PhpInk\Nami\CoreBundle\Model\CategoryInterface;
use PhpInk\Nami\CoreBundle\Model\UserInterface;

class CategoryRepository extends OrmRepository implements CategoryRepositoryInterface
{
    use MaterializedPathRepositoryTrait;

    protected $orderByFields = array(
        'default' => array('this.path', 'this.position')
    );

    protected $filterByFields = array(
        'id' => 'this.id',
        'name' => 'this.name',
        'slug' => 'this.slug',
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
        $this->initializeTreeRepository($em, $class);
    }

    /**
     * Item get_one returning an Document
     * checking its accessibility
     *
     * @param int           $id
     * @param UserInterface $user
     *
     * @return CategoryInterface
     */
    public function getItem($id, UserInterface $user = null)
    {
        $query = $this->createQueryBuilder('this');
        $query = $this->buildItemsQuery($query, $user);
        // Get category with children
        // ie: WHERE id = 4 OR path LIKE 4,% OR path LIKE %,4,%
        $query
            ->where('this.id = :id')->setParameter('id', intval($id))
            ->orWhere($query->expr()->like('this.path', $query->expr()->literal('%'. intval($id). ',%')))
            ->orWhere($query->expr()->like('this.path', $query->expr()->literal('%,'. intval($id). ',%')))
            ->orderBy('this.position')
            ->orderBy('this.path');


        $category = $this->buildCategoryTree(
            $query->getQuery()->getResult(), $id
        );
        return $category;
    }

    public function getCategoryTree(UserInterface $user = null, $orderBy = array(), $filterBy = array())
    {
        $categories = $this->getItemsQuery(
            $user, $orderBy, $filterBy
        )->getQuery()->getResult();

        return $this->buildCategoryTree($categories);
    }

    public function getMenu()
    {
        $query = $this
            ->getItemsQuery()
            ->leftJoin('this.pages', 'pages');
        $this->addWhereClause($query, 'pages.active', true);
        $menu = $query->getQuery()->getResult();
        return $menu;
    }

    public function getCategoryTreePaginated(UserInterface $user = null, $orderBy = array(), $filterBy = array())
    {
        return new Collection(
            new ArrayCollection(
                $this->childrenHierarchy()
                /*$this->getCategoryTree(
                    $user, $orderBy, $filterBy
                )*/
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
            /** @var CategoryInterface $category */
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
            /** @var CategoryInterface $category */
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
            ->select('this.slug', 'this.name')
            ->where('this.level = :level')
            ->andWhere('this.active = :active')
            ->setParameters(array(
                'level' => 0,
                'active' => true
            ));

        return $query->getQuery()->getResult(
            AbstractQuery::HYDRATE_ARRAY
        );

    }

    public function getCategoryFromSlug($slug)
    {
        $query = $this->createQueryBuilder('this')
            ->where('this.level = :level')
            ->andWhere('this.slug = :slug')
            ->setParameters(
                array(
                    'slug' => $slug,
                    'level' => 0
                )
            );

        $entity = $this->fetchSingleResult($query);
        return $entity;

    }
}
