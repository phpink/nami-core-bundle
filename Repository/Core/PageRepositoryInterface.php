<?php

namespace PhpInk\Nami\CoreBundle\Repository\Core;

use PhpInk\Nami\CoreBundle\Repository\RepositoryInterface;

/**
 * Interface PageRepositoryInterface
 *
 * @package PhpInk\Nami\CoreBundle\Repository\Odm
 */
interface PageRepositoryInterface extends RepositoryInterface
{
    public function getPageRoutes();

    public function getPageFromSlug($slug);

    public function getLastUpdate();
}
