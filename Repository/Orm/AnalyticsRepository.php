<?php

namespace PhpInk\Nami\CoreBundle\Repository\Orm;

use PhpInk\Nami\CoreBundle\Repository\Orm\AbstractRepository as OrmRepository;

class AnalyticsRepository extends OrmRepository
{
    /**
     * Retrieves Search Analytics
     *
     * @param int   $offset
     * @param int   $limit
     * @param array $orderBy
     * @param array $filterBy
     *
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getPageViewsAnalytics(
        $offset = null, $limit = null, $orderBy = array(), $filterBy = array()
    ) {
        $this->filterByFields = array_merge(
            $this->filterByFields,
            array(
                'page' => 'this.page',
                'ip' => 'this.ip',
                'userAgent' => 'this.userAgent',
                'date' => 'this.createdAt'
            )
        );
        $this->orderByFields = array_merge(
            array('hits' => 'hits'),
            $this->orderByFields
        );

        $query = $this->createQueryBuilder('this')
            ->select(
                'this',
                'COUNT(DISTINCT id) as hits'
            )
            ->groupBy('this.search');

        $query = $this->getItemsQueryOrderBy($query, $orderBy);
        $query = $this->getItemsQueryFilterBy($query, $filterBy);
        return $this->paginateQuery($query, $offset, $limit);
    }
    /**
     * Retrieves Search Analytics
     *
     * @param int   $offset
     * @param int   $limit
     * @param array $orderBy
     * @param array $filterBy
     *
     * @return \Doctrine\ORM\Tools\Pagination\Paginator
     */
    public function getSearchAnalytics(
        $offset = null, $limit = null, $orderBy = array(), $filterBy = array()
    ) {
        $this->filterByFields = array_merge(
            $this->filterByFields,
            array(
                'user' => 'this.user',
                'search' => 'this.search',
                'date' => 'this.createdAt'
            )
        );
        $this->orderByFields = array_merge(
            array('hits' => 'hits'),
            $this->orderByFields
        );

        $query = $this->createQueryBuilder('this')
            ->select(
                'this',
                'COUNT(DISTINCT id) as hits'
            )
            ->groupBy('this.search');

        $query = $this->getItemsQueryOrderBy($query, $orderBy);
        $query = $this->getItemsQueryFilterBy($query, $filterBy);
        return $this->paginateQuery($query, $offset, $limit);
    }
}
