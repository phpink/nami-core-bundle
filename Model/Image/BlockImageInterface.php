<?php

namespace PhpInk\Nami\CoreBundle\Model\Image;

use PhpInk\Nami\CoreBundle\Model\BlockInterface;
use PhpInk\Nami\CoreBundle\Model\ImageInterface;

/**
 * Image interface
 */
interface BlockImageInterface extends ImageInterface
{

    /**
     * Set block.
     *
     * @param BlockInterface $block
     * @return $this
     */
    public function setBlock(BlockInterface $block);

    /**
     * Get block.
     *
     * @return BlockInterface
     */
    public function getBlock();
}
