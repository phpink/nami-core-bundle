<?php

namespace PhpInk\Nami\CoreBundle\Repository\Odm;

use PhpInk\Nami\CoreBundle\Repository\OdmRepository;

class AnalyticsRepository extends OdmRepository
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
    public function getSearchAnalytics($offset = null, $limit = null, $orderBy = array(), $filterBy = array())
    {
        $this->filterByFields = array_merge(
            $this->filterByFields,
            array(
                'user' => 'user',
                'search' => 'search',
                'date' => 'createdAt'
            )
        );
        self::$orderByFields = array_merge(
            array('hits' => 'hits'),
            $this->orderByFields
        );

        $query = $this->createQueryBuilder('this')
            ->select(
                'this',
                'COUNT(DISTINCT id) as hits'
            )
            ->groupBy('search');

        $query = $this->getItemsQueryOrderBy($query, $orderBy);
        $query = $this->getItemsQueryFilterBy($query, $filterBy);
        return $this->paginateQuery($query, $offset, $limit);
    }
}
