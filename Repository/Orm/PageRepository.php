<?php

namespace PhpInk\Nami\CoreBundle\Repository\Orm;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use PhpInk\Nami\CoreBundle\Repository\Orm\AbstractRepository as OrmRepository;
use PhpInk\Nami\CoreBundle\Repository\Core\PageRepositoryInterface;
use PhpInk\Nami\CoreBundle\Model\UserInterface;

class PageRepository extends OrmRepository implements PageRepositoryInterface
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
     * @param mixed         $query The doctrine query builder.
     * @param UserInterface $user  The user who made the request.
     *
     * @return QueryBuilder
     */
    public function buildItemsQuery($query, UserInterface $user = null)
    {
        $query->addSelect(
            'category', 'background',
            'blocks', 'blockImages'
        );
        $query
            ->leftJoin('this.category', 'category')
            ->leftJoin('this.background', 'background')
            ->leftJoin('this.blocks', 'blocks')
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
            ->orderBy('blocks.position', 'asc')
            ;//->orderBy('blockImages.position', 'asc');
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
