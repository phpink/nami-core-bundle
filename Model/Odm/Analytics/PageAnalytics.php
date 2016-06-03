<?php

namespace PhpInk\Nami\CoreBundle\Model\Odm\Analytics;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use JMS\Serializer\Annotation as JMS;
use PhpInk\Nami\CoreBundle\Model\Odm\Core;
use PhpInk\Nami\CoreBundle\Model\Odm\Page;

/**
 * Model\Odm\Analytics\PageAnalytics
 *
 * @ODM\Document(
 *     collection="analytics_page",
 *     repositoryClass="PhpInk\Nami\CoreBundle\Repository\Odm\AnalyticsRepository"
 * )
 * @JMS\ExclusionPolicy("all")
 */
class PageAnalytics extends BaseAnalytics
{
    /**
     * The page seen
     *
     * @var Page
     * @ODM\ReferenceOne(targetDocument="PhpInk\Nami\CoreBundle\Model\Odm\Page")
     * @JMS\Type("integer")
     * @JMS\Accessor("getPageId")
     */
    protected $page;


    /**
     * Constructor
     *
     * @param Page $page [optional]
     * @param int  $ip [optional]
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
     * Get Page entity related by `page` (many to one).
     *
     * @return Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Get the page ID.
     *
     * @return int
     */
    public function getPageId()
    {
        return $this->getPage() ?
            $this->getPage()->getId() : null;
    }
}
