<?php

namespace PhpInk\Nami\CoreBundle\Model\Orm\Image;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use PhpInk\Nami\CoreBundle\Model\Orm\Core;
use PhpInk\Nami\CoreBundle\Model\Image\BackgroundInterface;
use PhpInk\Nami\CoreBundle\Model\Orm\Image;
use PhpInk\Nami\CoreBundle\Model\Orm\Page;
use PhpInk\Nami\CoreBundle\Model\PageInterface;

/**
 * Entity\Image
 *
 * @ORM\Entity(repositoryClass="PhpInk\Nami\CoreBundle\Repository\Orm\Image\BackgroundRepository")
 * @ORM\Table(name="background")
 * @ORM\HasLifecycleCallbacks()
 * @JMS\ExclusionPolicy("all")
 */
class Background extends Image implements BackgroundInterface
{
    use Core\SortableItemTrait,
        Core\CreatedUpdatedAtTrait,
        Core\CreatedUpdatedByTrait;

    const DEFAULT_SUBFOLDER = 'background';
    
    /**
     * @var Page
     * @ORM\OneToOne(targetEntity="PhpInk\Nami\CoreBundle\Model\Orm\Page", inversedBy="background")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", nullable=false)
     */
    protected $page;

    /**
     * Set page.
     *
     * @param PageInterface $page
     * @return $this
     */
    public function setPage(PageInterface $page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page.
     *
     * @return PageInterface
     */
    public function getPage()
    {
        return $this->page;
    }
}
