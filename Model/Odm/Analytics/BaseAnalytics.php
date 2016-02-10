<?php

namespace PhpInk\Nami\CoreBundle\Model\Odm\Analytics;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use JMS\Serializer\Annotation as JMS;
use PhpInk\Nami\CoreBundle\Model\Odm\Core;

/**
 * Base analytics (ip, createdAt)
 *
 * @ODM\MappedSuperclass
 */
class BaseAnalytics extends Core\Document
{
    /**
     * Primary Key
     * @var string
     * @ODM\Id
     * @JMS\Expose
     */
    protected $id;

    /**
     * The ip who have seen the page
     *
     * @var string
     * @ODM\String
     * @JMS\Expose
     */
    protected $ip;

    /**
     * The user agent
     *
     * @var string
     * @ODM\String
     * @JMS\Expose
     */
    protected $userAgent;

    /**
     * @var \DateTime
     * @ODM\Date
     * @JMS\Expose
     */
    protected $createdAt;

    /**
     * Constructor
     *
     * @param string $ip [optional]
     */
    public function __construct($ip = null)
    {
        $this->setCreatedAt(new \DateTime());
        if (is_string($ip)) {
            $this->setIp($ip);
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
