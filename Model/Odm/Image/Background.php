<?php

namespace PhpInk\Nami\CoreBundle\Model\Odm\Image;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use JMS\Serializer\Annotation as JMS;
use PhpInk\Nami\CoreBundle\Model\Orm\Core;
use PhpInk\Nami\CoreBundle\Model\Image\BackgroundInterface;
use PhpInk\Nami\CoreBundle\Model\Odm\Image;
use PhpInk\Nami\CoreBundle\Model\Odm\Page;
use PhpInk\Nami\CoreBundle\Model\PageInterface;

/**
 * Document\Image
 *
 * @ODM\Document(
 *     collection="backgrounds",
 *     repositoryClass="PhpInk\Nami\CoreBundle\Repository\Odm\Image\BackgroundRepository"
 * )
 * @ODM\HasLifecycleCallbacks
 * @JMS\ExclusionPolicy("all")
 */
class Background extends Image implements BackgroundInterface
{
    use Core\CreatedUpdatedAtTrait,
        Core\CreatedUpdatedByTrait;

    const DEFAULT_SUBFOLDER = 'background';
    
    /**
     * @var Page
     * @ODM\ReferenceOne(targetDocument="PhpInk\Nami\CoreBundle\Model\Odm\Page", inversedBy="background")
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
