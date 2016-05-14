<?php

namespace PhpInk\Nami\CoreBundle\Repository\Orm;

use Doctrine\DBAL\Query\QueryBuilder;
use PhpInk\Nami\CoreBundle\Repository\Orm\AbstractRepository as OrmRepository;
use PhpInk\Nami\CoreBundle\Repository\Core\UserRepositoryInterface;
use PhpInk\Nami\CoreBundle\Model\UserInterface;

/**
 * Class UserRepository
 *
 * @package PhpInk\Nami\CoreBundle\Repository\Orm
 */
class UserRepository extends OrmRepository implements UserRepositoryInterface
{
    protected $orderByFields = array(
        'username' => 'this.username',
        'id' => 'this.id',
        'nameFirst' => 'this.nameFirst',
        'nameLast' => 'this.nameLast',
        'male' => 'this.male',
        'active' => 'this.active',
        'locked' => 'this.locked',
        'email' => 'this.email',
        'phone' => 'this.phone',
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
        //$query->addSelect('userImage');
        //$query->leftJoin('this.avatar', 'userImage');
        return $query;
    }

    /**
     * Finds a user either by email, or username
     *
     * @param string  $usernameOrEmail The email or username of the user.
     * @param boolean $filterActive    Retrieve only active users or all users.
     *
     * @return UserInterface
     */
    public function findUserByUsernameOrEmail(
        $usernameOrEmail, $filterActive = false
    ) {
        $query = $this->createQueryBuilder('this');
        $query = $this->buildItemsQuery($query);
        $query->where('this.username = :usernameOrEmail');

        if (filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)) {
            $query->orWhere('this.email = :usernameOrEmail');
        }
        if ($filterActive) {
            $query->andWhere('this.active = true AND this.locked = false');
        }
        $query->setParameter('usernameOrEmail', $usernameOrEmail);
        return $this->fetchSingleResult($query);
    }

    /**
     * Finds a user by confirmation token
     *
     * @param string $token The confirmation token of the user.
     *
     * @return UserInterface
     */
    public function findUserByConfirmationToken($token)
    {
        $query = $this->createQueryBuilder('this');
        $query = $this->buildItemsQuery($query);
        $query
            ->where('this.confirmationToken = :token')
            ->setParameter('token', $token);

        return $this->fetchSingleResult($query);
    }

    /**
     * Finds a user by its id
     *
     * @param int $id The id of the user.
     *
     * @return UserInterface
     */
    public function findUserById($id)
    {
        $query = $this->createQueryBuilder('this');
        $query = $this->buildItemsQuery($query);
        $query
            ->where('this.id = :id')
            ->setParameter('id', $id);
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
                'this' => 'this.id',
                'user' => 'this.id',
                'date' => 'analytics.createdAt'
            )
        );
        self::$orderByFields = array_merge(
            array('hits' => 'hits'),
            self::$orderByFields
        );

        $query = $this->createQueryBuilder('this');
        $query = $this->buildItemsQuery($query, null, true)
            ->innerJoin('this.loginAnalytics', 'analytics')
            ->addSelect(
                'COUNT(DISTINCT analytics.id) as hits'
            )
            ->groupBy('id');

        $query = $this->getItemsQueryOrderBy($query, $orderBy);
        $query = $this->getItemsQueryFilterBy($query, $filterBy);
        return $this->paginateQuery($query, $offset, $limit);
    }
}
