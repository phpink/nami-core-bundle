<?php

namespace PhpInk\Nami\CoreBundle\Model\Orm\Analytics;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use PhpInk\Nami\CoreBundle\Model\Orm\Core;
use PhpInk\Nami\CoreBundle\Model\Orm\Page;

/**
 * Orm\Analytics\Profile
 *
 * @ORM\Entity(repositoryClass="PhpInk\Nami\CoreBundle\Repository\Orm\AnalyticsRepository")
 * @ORM\Table(
 *     name="analytics_page",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="analytics_page_ip_unique", columns={"page_id", "ip"})
 *     }
 * )
 * @JMS\ExclusionPolicy("all")
 */
class PageAnalytics extends BaseAnalytics
{

    /**
     * The page seen
     *
     * @var Page
     * @ORM\ManyToOne(
     *     targetEntity="PhpInk\Nami\CoreBundle\Model\Orm\Page",
     *     inversedBy="pageAnalytics"
     * )
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     * @JMS\Type("integer")
     * @JMS\Accessor("getPageId")
     */
    protected $page;

    /**
     * Constructor
     *
     * @param Page   $page [optional]
     * @param int    $ip [optional]
     * @param string $userAgent [optional]
     */
    public function __construct(Page $page = null, $ip = null, $userAgent = null)
    {
        parent::__construct($ip, $userAgent);
        if ($page instanceof Page) {
            $this->setPage($page);
        }
    }

    /**
     * Set Page entity related by `page` (many to one).
     *
     * @param Page $page
     * @return PageAnalytics
     */
    public function setPage(Page $page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get Page entity related by `Page` (many to one).
     *
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Get the Page ID.
     *
     * @return int
     */
    public function getPageId()
    {
        return $this->getPage() ?
            $this->getPage()->getId() : null;
    }
}

