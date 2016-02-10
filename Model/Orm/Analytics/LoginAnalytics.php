<?php

namespace PhpInk\Nami\CoreBundle\Model\Orm\Analytics;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use PhpInk\Nami\CoreBundle\Model\Orm\User;

/**
 * Analytics\Login
 *
 * @ORM\Entity(repositoryClass="PhpInk\Nami\CoreBundle\Repository\Orm\AnalyticsRepository")
 * @ORM\Table(
 *     name="analytics_login"
 * )
 * @JMS\ExclusionPolicy("all")
 */
class LoginAnalytics extends BaseAnalytics
{
    /**
     * The user who have seen the product
     *
     * @var User
     * @ORM\ManyToOne(
     *     targetEntity="PhpInk\Nami\CoreBundle\Model\Orm\User",
     *     inversedBy="loginAnalytics"
     * )
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
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
     * @return ResellerLoginAnalytics
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
