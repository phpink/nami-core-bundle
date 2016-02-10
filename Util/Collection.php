<?php

namespace PhpInk\Nami\CoreBundle\Util;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Common\Collections\Criteria;
use JMS\Serializer\Annotation as JMS;

/**
 * Class Collection
 *
 * @package PhpInk\Nami\CoreBundle\Util
 *
 * @JMS\ExclusionPolicy("all")
 */
class Collection extends AbstractLazyCollection implements Selectable
{

    /**
     * @var int|null
     * @JMS\Expose
     */
    protected $count;

    /**
     * The backed collection to use
     *
     * @var Collection
     * @JMS\Expose
     * @JMS\SerializedName("elements")
     */
    protected $collection;

    /**
     * The API route name (get_all)
     * @var string
     * @JMS\Exclude
     */
    protected $route;

    /**
     * Initializes a new Collection.
     *
     * @param ArrayCollection $elements [optional]
     * @param string $route
     * @param int $routeId
     */
    public function __construct($elements = null, $route, $routeId = null)
    {
        $this->route = $route;
        $this->routeId = $routeId;
        $this->count = 0;
        if (!is_null($elements)) {
            $this->collection = $elements;
            $this->count = count($elements);
        }
        $this->initialize();
    }

    public function doInitialize()
    {
        if (is_null($this->collection)) {
            $this->collection = new ArrayCollection();
        }
    }

    /**
     * Get the value of count.
     *
     * @return int|null
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Set the value of count.
     *
     * @param int|null $count
     * @return Collection
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function matching(Criteria $criteria)
    {
        return $this->collection->matching($criteria);
    }

    /**
     * Get the value of route.
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set the value of route.
     *
     * @param string
     * @return Collection
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get the value of routeId.
     *
     * @return int|null
     */
    public function getRouteId()
    {
        return $this->routeId;
    }

    /**
     * Set the value of routeId.
     *
     * @param int|null $routeId
     * @return Collection
     */
    public function setRouteId($routeId)
    {
        $this->routeId = $routeId;
        return $this;
    }

    /**
     * Get the value of collection.
     *
     * @return Collection
     */
    public function getElements()
    {
        return $this->collection;
    }

    /**
     * Set the value of collection.
     *
     * @param array|Collection $collection
     * @return Collection
     */
    public function setElements($collection)
    {
        if (is_array($collection)) {
            $collection = new ArrayCollection($collection);
        }
        $this->collection = $collection;
        return $this;
    }

}
