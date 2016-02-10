<?php

namespace PhpInk\Nami\CoreBundle\Model\Odm\Analytics;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use JMS\Serializer\Annotation as JMS;
use PhpInk\Nami\CoreBundle\Model\Odm\Core;
use PhpInk\Nami\CoreBundle\Model\Odm\User;

/**
 * Document\Analytics\Login
 *
 * @ODM\Document(
 *     collection="countries",
 *     repositoryClass="PhpInk\Nami\CoreBundle\Repository\Odm\AnalyticsRepository"
 * )
 * @JMS\ExclusionPolicy("all")
 */
class LoginAnalytics extends BaseAnalytics
{
    /**
     * The user who have seen the product
     *
     * @var User
     * @ODM\ReferenceOne(targetDocument="PhpInk\Nami\CoreBundle\Model\Odm\User", inversedBy="loginAnalytics")
     * @JMS\Type("integer")
     * @JMS\Accessor("getUserId")
     */
    protected $user;

    /**
     * Constructor
     *
     * @param User   $user [optional]
     * @param string $userAgent [optional]
     */
    public function __construct(User $user = null, $userAgent = null)
    {
        parent::__construct($user->getIp(), $userAgent);
        if ($user instanceof User) {
            $this->setUser($user);
        }
    }

    /**
     * Set User entity related by `user` (many to one).
     *
     * @param User $user
     * @return LoginAnalytics
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get User entity related by `user` (many to one).
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get the user ID.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->getUser() ?
            $this->getUser()->getId() : null;
    }
}
