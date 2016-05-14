<?php

namespace PhpInk\Nami\CoreBundle\Model\Odm\Image;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use JMS\Serializer\Annotation as JMS;
use PhpInk\Nami\CoreBundle\Model\Orm\Core;
use PhpInk\Nami\CoreBundle\Model\Image\BlockImageInterface;
use PhpInk\Nami\CoreBundle\Model\Odm\Image;
use PhpInk\Nami\CoreBundle\Model\Odm\Block;
use PhpInk\Nami\CoreBundle\Model\BlockInterface;

/**
 * Document\Image
 *
 * @ODM\Document(
 *     collection="block_images",
 *     repositoryClass="PhpInk\Nami\CoreBundle\Repository\Odm\Image\BlockImageRepository"
 * )
 * @ODM\HasLifecycleCallbacks
 * @JMS\ExclusionPolicy("all")
 */
class BlockImage extends Image implements BlockImageInterface
{
    use Core\SortableItemTrait,
        Core\CreatedUpdatedAtTrait,
        Core\CreatedUpdatedByTrait;

    const DEFAULT_SUBFOLDER = 'block';
    
    /**
     * @var Block
     * @ODM\ReferenceOne(targetDocument="PhpInk\Nami\CoreBundle\Model\Orm\Block", inversedBy="images")
     */
    protected $block;

    /**
     * @var integer
     * @Gedmo\Sortable(groups={"block"})
     * @ODM\Int
     * @ODM\Index
     * @JMS\Expose
     */
    private $position = 0;

    /**
     * Set block.
     *
     * @param BlockInterface $block
     * @return $this
     */
    public function setBlock(BlockInterface $block)
    {
        $this->block = $block;

        return $this;
    }

    /**
     * Get block.
     *
     * @return BlockInterface
     */
    public function getBlock()
    {
        return $this->block;
    }
}
