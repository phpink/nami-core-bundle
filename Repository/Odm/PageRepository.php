<?php

namespace PhpInk\Nami\CoreBundle\Repository\Odm;

use PhpInk\Nami\CoreBundle\Repository\OdmRepository;

class PageRepository extends OdmRepository
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
        $query = $this
            ->createQueryBuilder('this')
            ->select('slug', 'category')
            ->field('active')->equals(true);

        return $query->getQuery()->toArray();

    }

    public function getPageFromSlug($slug)
    {
        $query = $this->createQueryBuilder('this')
            ->field('active')->equals(true)
            ->field('slug')->equals($slug)
            ->sort('blocks.position', 'asc');

        $entity = $this->fetchSingleResult($query);
        return $entity;

    }

    public function getLastUpdate()
    {
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
