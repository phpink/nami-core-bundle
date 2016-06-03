<?php

namespace PhpInk\Nami\CoreBundle\Model\Orm\Image;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use PhpInk\Nami\CoreBundle\Model\Orm\Core;
use PhpInk\Nami\CoreBundle\Model\BlockInterface;
use PhpInk\Nami\CoreBundle\Model\Image\BlockImageInterface;
use PhpInk\Nami\CoreBundle\Model\Orm\Block;
use PhpInk\Nami\CoreBundle\Model\Orm\Image;

/**
 * Entity\Image
 *
 * @ORM\Entity(repositoryClass="PhpInk\Nami\CoreBundle\Repository\Orm\Image\BlockImageRepository")
 * @ORM\Table(name="block_image")
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\ManyToOne(targetEntity="PhpInk\Nami\CoreBundle\Model\Orm\Block", inversedBy="images")
     * @ORM\JoinColumn(name="block_id", referencedColumnName="id", nullable=false)
     */
    protected $block;

    /**
     * @var integer
     * @Gedmo\Sortable(groups={"block"})
     * @ORM\Column(name="position", type="integer")
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
