<?php

namespace PhpInk\Nami\CoreBundle\Util;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * ContainerAware trait.
 *
 * @package PhpInk\Nami\CoreBundle\Util
 */
trait ContainerBuilderAwareTrait
{
    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * Sets the container.
     *
     * @param ContainerBuilder $container A Container builder
     */
    public function setContainer(ContainerBuilder $container)
    {
        $this->container = $container;
    }
}
