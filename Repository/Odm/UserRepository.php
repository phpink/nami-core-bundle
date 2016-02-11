<?php

namespace PhpInk\Nami\CoreBundle\Repository\Odm;

use PhpInk\Nami\CoreBundle\Repository\Odm\AbstractRepository as OdmRepository;
use PhpInk\Nami\CoreBundle\Repository\Core\UserRepositoryInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRepository extends OdmRepository implements UserRepositoryInterface
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
     * @return UserInterface
     */
    public function findUserByUsernameOrEmail($usernameOrEmail, $filterActive = false)
    {
        /** @var \Doctrine\ODM\MongoDB\Query\Builder $query */
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
     * @return UserInterface
     */
    public function findUserByConfirmationToken($token)
    {
        /** @var \Doctrine\ODM\MongoDB\Query\Builder $query */
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
     * @return UserInterface
     */
    public function findUserById($id)
    {
        /** @var \Doctrine\ODM\MongoDB\Query\Builder $query */
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
     * @return object
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
