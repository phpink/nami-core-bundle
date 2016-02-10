<?php

namespace PhpInk\Nami\CoreBundle\Model\Odm\Analytics;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use JMS\Serializer\Annotation as JMS;
use PhpInk\Nami\CoreBundle\Model\Odm\Core;
use PhpInk\Nami\CoreBundle\Model\Odm\User;

/**
 * Document\Analytics\Search
 *
 * @ODM\Document(
 *     collection="countries",
 *     repositoryClass="PhpInk\Nami\CoreBundle\Repository\Odm\AnalyticsRepository"
 * )
 * @JMS\ExclusionPolicy("all")
 */
class SearchAnalytics extends BaseAnalytics
{
    /**
     * @var string
     * @ODM\String
     * @JMS\Expose
     */
    protected $search;

    /**
     * Constructor
     *
     * @param string  $search [optional]
     * @param string  $ip     [optional]
     * @param string  $userAgent [optional]
     */
    public function __construct($search = null, $ip = null, $userAgent = null)
    {
        parent::__construct($ip, $userAgent);
        if ($search) {
            $this->setSearch($search);
        }
    }

    /**
     * Set the value of search.
     *
     * @param string $search
     * @return SearchAnalytics
     */
    public function setSearch($search)
    {
        $this->search = $search;

        return $this;
    }

    /**
     * Get the product ID.
     *
     * @return int
     */
    public function getSearch()
    {
        return $this->search;
    }
}
