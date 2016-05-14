<?php

namespace PhpInk\Nami\CoreBundle\Model\Image;

use PhpInk\Nami\CoreBundle\Model\ImageInterface;
use PhpInk\Nami\CoreBundle\Model\PageInterface;

/**
 * Image interface
 */
interface BackgroundInterface extends ImageInterface
{

    /**
     * Set page.
     *
     * @param PageInterface $page
     * @return $this
     */
    public function setPage(PageInterface $page);

    /**
     * Get page.
     *
     * @return PageInterface
     */
    public function getPage();
}
