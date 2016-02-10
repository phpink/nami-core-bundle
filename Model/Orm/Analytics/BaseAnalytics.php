<?php

namespace PhpInk\Nami\CoreBundle\Model\Orm\Analytics;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use PhpInk\Nami\CoreBundle\Model\Orm\Core;

/**
 * Base analytics (ip, createdAt)
 *
 * @ORM\MappedSuperclass
 */
class BaseAnalytics extends Core\Entity
{
    /**
     * Primary Key
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    protected $id;

    /**
     * The ip who have seen the page
     *
     * @var string
     * @ORM\Column(name="ip", type="string", length=255)
     * @JMS\Expose
     */
    protected $ip;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     * @JMS\Expose
     */
    protected $createdAt;

    /**
     * The user agent
     *
     * @var string
     * @ORM\Column(name="user_agent", type="string", length=255)
     * @JMS\Expose
     */
    protected $userAgent;

    /**
     * Constructor
     *
     * @param string $ip [optional]
     * @param string $userAgent [optional]
     */
    public function __construct($ip = null, $userAgent = null)
    {
        $this->setCreatedAt(new \DateTime());
        if (is_string($ip)) {
            $this->setIp($ip);
        }
        if (is_string($userAgent)) {
            $this->setUserAgent($userAgent);
        }
    }

    /**
     * Get the value of id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id.
     *
     * @param integer
     * @return BaseAnalytics
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set Ip entity related by `ip` (many to one).
     *
     * @param string $ip
     * @return BaseAnalytics
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get Ip entity related by `ip` (many to one).
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Get the value of userAgent
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Set the value of userAgent
     * @param string $userAgent
     * @return BaseAnalytics
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return BaseAnalytics
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
