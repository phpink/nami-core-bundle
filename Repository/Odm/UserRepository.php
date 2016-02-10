<?php

namespace PhpInk\Nami\CoreBundle\Repository\Odm;

use Doctrine\ODM\MongoDB\Query\Builder as QueryBuilder;
use PhpInk\Nami\CoreBundle\Repository\OdmRepository;
use PhpInk\Nami\CoreBundle\Model\User;

class UserRepository extends OdmRepository
{
    protected $orderByFields = array(
        'username' => 'username',
        'id' => 'id',
        'nameFirst' => 'nameFirst',
        'nameLast' => 'nameLast',
        'male' => 'male',
        'active' => 'active',
        'locked' => 'locked',
        'email' => 'email',
        'phone' => 'phone',
        'createdAt' => 'createdAt',
        'updatedAt' => 'updatedAt'
    );

    /**
     * Finds a user either by email, or username
     *
     * @param string  $usernameOrEmail
     * @param boolean $filterActive
     * @return User
     */
    public function findUserByUsernameOrEmail($usernameOrEmail, $filterActive = false)
    {
        $query = $this->createQueryBuilder('this');
        $query = $this->buildItemsQuery($query);
        $query->field('username')->equals($usernameOrEmail);

        if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
            $query->addOr(
              $query->expr()->field('email')->equals($usernameOrEmail)
            );
        }
        if ($filterActive) {
            $query->addAnd(
                $query->expr()->field('active')->equals(true)
            );
            $query->addAnd(
                $query->expr()->field('locked')->equals(false)
            );
        }
        return $this->fetchSingleResult($query);
    }

    /**
     * Finds a user by confirmation token
     *
     * @param string $token
     *
     * @return User
     */
    public function findUserByConfirmationToken($token)
    {
        $query = $this->createQueryBuilder('this');
        $query = $this->buildItemsQuery($query);
        $query
            ->field('confirmationToken')
            ->equals( $token);

        return $this->fetchSingleResult($query);
    }

    /**
     * Finds a user by its id
     *
     * @param int $id
     *
     * @return User
     */
    public function findUserById($id)
    {
        $query = $this->createQueryBuilder('this');
        $query = $this->buildItemsQuery($query);
        $query
            ->field('id')
            ->equals($id);
        return $this->fetchSingleResult($query);
    }

    /**
     * Retrieves Reseller login analytics
     *
     * @param int   $offset
     * @param int   $limit
     * @param array $orderBy
     * @param array $filterBy
     *
     * @return QueryBuilder
     */
    public function getLoginAnalytics($offset = null, $limit = null, $orderBy = array(), $filterBy = array())
    {
        self::$filterByFields = array_merge(
            self::$filterByFields,
            array(
                'this' => 'id',
                'user' => 'id',
                'date' => 'analytics.createdAt'
            )
        );
        self::$orderByFields = array_merge(
            array('hits' => 'hits'),
            self::$orderByFields
        );

        $query = $this->createQueryBuilder('this');
        $query = $this->buildItemsQuery($query, null, true)
            ->innerJoin('loginAnalytics', 'analytics')
            ->addSelect(
                'COUNT(DISTINCT analytics.id) as hits'
            )
            ->groupBy('id');

        $query = $this->getItemsQueryOrderBy($query, $orderBy);
        $query = $this->getItemsQueryFilterBy($query, $filterBy);
        return $this->paginateQuery($query, $offset, $limit);
    }
}
