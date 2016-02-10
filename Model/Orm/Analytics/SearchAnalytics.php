<?php

namespace PhpInk\Nami\CoreBundle\Model\Orm\Analytics;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use PhpInk\Nami\CoreBundle\Model\Orm\User;

/**
 * Document\Analytics\Search
 *
 * @ORM\Entity(repositoryClass="PhpInk\Nami\CoreBundle\Repository\Orm\AnalyticsRepository")
 * @ORM\Table(
 *     name="analytics_search"
 * )
 * @JMS\ExclusionPolicy("all")
 */
class SearchAnalytics extends BaseAnalytics
{
    /**
     * @var string
     * @ORM\Column(name="search", type="string", length=255)
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
