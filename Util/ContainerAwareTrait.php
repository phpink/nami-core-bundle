<?php

namespace PhpInk\Nami\CoreBundle\Util;
use Symfony\Component\DependencyInjection\Container;

/**
 * ContainerAware trait.
 *
 * @package PhpInk\Nami\CoreBundle\Util
 */
trait ContainerAwareTrait
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * Sets the container.
     *
     * @param Container $container A Container instance
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
}
