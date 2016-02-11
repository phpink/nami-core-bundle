<?php

namespace PhpInk\Nami\CoreBundle\Repository\Core;

use PhpInk\Nami\CoreBundle\Repository\RepositoryInterface;

/**
 * Interface ConfigurationRepositoryInterface
 *
 * @package PhpInk\Nami\CoreBundle\Repository
 */
interface ConfigurationRepositoryInterface extends RepositoryInterface
{
    /**
     * Get configuration values
     * @param $names
     * @return mixed
     */
    public function getValues($names);
}
