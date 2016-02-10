<?php

namespace PhpInk\Nami\CoreBundle\Util;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * Class PaginatedCollection
 *
 * @package PhpInk\Nami\CoreBundle\Util
 *
 * @JMS\ExclusionPolicy("all")
 *
 * @Hateoas\Relation(
 *   "self",
 *   href = @Hateoas\Route(
 *     "expr(object.getRoute())",
 *     parameters = {
 *       "offset" = "expr(object.getOffset())",
 *       "limit" = "expr(object.getLimit())",
 *       "id" = "expr(object.getRouteId())"
 *     }
 *   )
 * )
 * @Hateoas\Relation(
 *   "first",
 *   href = @Hateoas\Route(
 *     "expr(object.getRoute())",
 *     parameters = {
 *       "offset" = "0",
 *       "limit" = "expr(object.getLimit())",
 *       "id" = "expr(object.getRouteId())"
 *     }
 *   )
 * )
 * @Hateoas\Relation(
 *   "last",
 *   href = @Hateoas\Route(
 *     "expr(object.getRoute())",
 *     parameters = {
 *       "offset" = "expr(object.getLastPageOffset())",
 *       "limit" = "expr(object.getLimit())",
 *       "id" = "expr(object.getRouteId())"
 *     }
 *   )
 * )
 */
class PaginatedCollection extends Collection
{
    /**
     * @var int|null
     * @JMS\Expose
     */
    protected $offset;

    /**
     * @var int|null
     * @JMS\Expose
     */
    protected $limit;

    /**
     * @var int|null
     * @JMS\Expose
     */
    protected $count;

    /**
     * Initializes a new Collection.
     *
     * @param mixed        $elements     Query builder object
     * @param int          $offset
     * @param int          $limit
     * @param int          $count
     * @param string       $route
     * @param int          $routeId
     */
    public function __construct($elements, $offset, $limit, $count, $route = null, $routeId = null, \Closure $handleResult = null)
    {
        $this->route = $route;
        $this->routeId = $routeId;
        $this->offset = $offset;
        $this->limit = $limit;
        $this->count = $count;
        $this->collection = new ArrayCollection();
        foreach ($elements as $entity) {
            if (!is_null($handleResult)) {
                $entity = $handleResult($entity);
            }
            $this->collection->add($entity);
        }
    }

    /**
     * @return int|null
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int|null $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return int|null
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int|null $offset
     * @return Collection
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }

    public function getLastPage()
    {
        $lastPage = null;
        if (!is_null($this->getLimit())
         && !is_null($this->getCount())) {
            $lastPage = floor(
                $this->getCount() /
                $this->getLimit()
            );
        }
        return $lastPage;
    }

    public function getLastPageOffset()
    {
        $lastPageOffset = $this->getLastPage();
        if ($lastPageOffset) {
            $lastPageOffset = $lastPageOffset * $this->getLimit();
        }
        return $lastPageOffset;
    }
}
