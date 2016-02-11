<?php

namespace PhpInk\Nami\CoreBundle\Repository\Core;

use PhpInk\Nami\CoreBundle\Repository\RepositoryInterface;
use PhpInk\Nami\CoreBundle\Model\UserInterface;

/**
 * Interface PageRepositoryInterface
 *
 * @package PhpInk\Nami\CoreBundle\Repository\Odm
 */
interface UserRepositoryInterface extends RepositoryInterface
{

    /**
     * Finds a user either by email, or username
     *
     * @param string  $usernameOrEmail
     * @param boolean $filterActive
     * @return UserInterface
     */
    public function findUserByUsernameOrEmail($usernameOrEmail, $filterActive = false);

    /**
     * Finds a user by confirmation token
     *
     * @param string $token
     *
     * @return UserInterface
     */
    public function findUserByConfirmationToken($token);

    /**
     * Finds a user by its id
     *
     * @param int $id
     *
     * @return UserInterface
     */
    public function findUserById($id);

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
    public function getLoginAnalytics($offset = null, $limit = null, $orderBy = array(), $filterBy = array());
}
