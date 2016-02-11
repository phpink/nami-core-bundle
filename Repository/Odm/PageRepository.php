<?php

namespace PhpInk\Nami\CoreBundle\Repository\Odm;

use PhpInk\Nami\CoreBundle\Repository\Odm\AbstractRepository as OdmRepository;
use PhpInk\Nami\CoreBundle\Repository\Core\PageRepositoryInterface;

class PageRepository extends OdmRepository implements PageRepositoryInterface
{
    protected $orderByFields = array(
        'blocks.position' => 'blocks.position',
        'id' => 'id',
        'title' => 'title',
        'slug' => 'slug',
        'active' => 'active',
        'header' => 'header',
        'category' => 'category',
        'createdAt' => 'createdAt',
        'updatedAt' => 'updatedAt'
    );

    public function getPageRoutes()
    {
        /** @var \Doctrine\ODM\MongoDB\Query\Builder $query */
        $query = $this
            ->createQueryBuilder('this')
            ->select('slug', 'category')
            ->field('active')->equals(true);

        return $query->getQuery()->toArray();

    }

    public function getPageFromSlug($slug)
    {
        /** @var \Doctrine\ODM\MongoDB\Query\Builder $query */
        $query = $this->createQueryBuilder('this')
            ->field('active')->equals(true)
            ->field('slug')->equals($slug)
            ->sort('blocks.position', 'asc');

        $entity = $this->fetchSingleResult($query);
        return $entity;

    }

    public function getLastUpdate()
    {
        /** @var \Doctrine\ODM\MongoDB\Query\Builder $query */
        $query = $this->createQueryBuilder('this')
            ->select('updatedAt')
            ->sort('updatedAt', 'desc')
            ->limit(1);

        $page = $this->fetchSingleResult($query);
        $lastUpdate = null;
        if ($page) {
            $lastUpdate = $page->getUpdatedAt();
        }
        return $lastUpdate;

    }
}
