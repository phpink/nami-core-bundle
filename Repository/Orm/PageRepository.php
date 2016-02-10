<?php

namespace PhpInk\Nami\CoreBundle\Repository\Orm;

use PhpInk\Nami\CoreBundle\Model\Orm\User;
use PhpInk\Nami\CoreBundle\Repository\OrmRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;

class PageRepository extends OrmRepository
{
    protected $orderByFields = array(
        'blocks.position' => 'blocks.position',
        'id' => 'this.id',
        'title' => 'this.title',
        'slug' => 'this.slug',
        'active' => 'this.active',
        'header' => 'this.header',
        'category' => 'this.category',
        'createdAt' => 'this.createdAt',
        'updatedAt' => 'this.updatedAt'
    );

    /**
     * Build the items query (join, filters)
     *
     * @param QueryBuilder $query
     * @param User         $user
     *
     * @return QueryBuilder
     */
    public function buildItemsQuery(QueryBuilder $query, User $user = null)
    {
        $alias = 'this';
        $query->addSelect(
            'category', 'background',
            'blocks', 'blockImages'
        );
        $query
            ->leftJoin($alias . '.category', 'category')
            ->leftJoin($alias . '.background', 'background')
            ->leftJoin($alias . '.blocks', 'blocks')
            ->leftJoin('blocks.images', 'blockImages');

        return $query;
    }

    public function getPageRoutes()
    {
        $query = $this->createQueryBuilder('this')
            ->leftJoin('this.category', 'category')
            ->select('this.slug', 'category.id')
            ->where('this.active = :active')
            ->setParameter('active', true);

        return $query->getQuery()->getResult(
            AbstractQuery::HYDRATE_ARRAY
        );

    }

    public function getPageFromSlug($slug)
    {
        $query = $this->createQueryBuilder('this');
        $query = $this->buildItemsQuery($query, null)
            ->where('this.active = :active')
            ->andWhere('this.slug = :slug')
            ->setParameters(
                array(
                    'active' => true,
                    'slug' => $slug
                )
            )
            ->orderBy('blocks.position', 'asc');

        $entity = $this->fetchSingleResult($query);
        return $entity;

    }

    public function getLastUpdate()
    {
        $query = $this->createQueryBuilder('this')
            ->select('this.updatedAt')
            ->orderBy('this.updatedAt', 'desc')
            ->setMaxResults(1);

        $lastUpdate = $this->fetchSingleResult($query);
        return $lastUpdate;

    }
}
