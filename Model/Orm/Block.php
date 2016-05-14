<?php

namespace PhpInk\Nami\CoreBundle\Model\Orm;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use PhpInk\Nami\CoreBundle\Model\Orm\Core;
use PhpInk\Nami\CoreBundle\Model\BlockInterface;
use PhpInk\Nami\CoreBundle\Model\Image\BlockImageInterface;
use PhpInk\Nami\CoreBundle\Model\PageInterface;
use PhpInk\Nami\CoreBundle\Model\UserInterface;

/**
* Document\Block
 *
 * @ORM\Entity(repositoryClass="PhpInk\Nami\CoreBundle\Repository\Orm\BlockRepository")
 * @ORM\Table(name="block")
 * @ORM\HasLifecycleCallbacks
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("custom", custom = {
 *     "id", "active", "title", "slug", "header",
 *     "content", "background", "category",
 *     "createdAt", "updatedAt", "createdBy", "updatedBy"
 * })
 */
class Block extends Core\Entity implements BlockInterface
{
    use Core\SortableItemTrait,
        Core\CreatedUpdatedAtTrait,
        Core\CreatedUpdatedByTrait;

    /**
     * Primary Key
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    protected $id;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     * @JMS\Expose
     * @JMS\Groups({"full"})
     */
    protected $active;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     */
    protected $content;

    /**
     * @var Page
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="blocks")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", nullable=false)
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\Accessor("getPageId")
     */
    protected $page;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose
     */
    protected $type = 'default';

    /**
     * @var Collection<PhpInk\Nami\CoreBundle\Model\Orm\Image\BlockImage>
     * @ORM\OneToMany(
     *     targetEntity="PhpInk\Nami\CoreBundle\Model\Orm\Image\BlockImage",
     *     mappedBy="block",
     *     cascade={"persist", "remove"}
     * )
     * @JMS\Expose
     * @JMS\Type("array<integer>")
     * @JMS\Accessor("getImagesId")
     */
    protected $images;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose
     */
    private $template = 'default';

    /**
     * Plugin instance
     * @var mixed
     */
    protected $plugin = null;

    /**
     * @var integer
     * @Gedmo\Sortable(groups={"page"})
     * @ORM\Column(name="position", type="integer")
     * @JMS\Expose
     */
    private $position = 0;

    /**
     * @var array
     * @JMS\Expose
     * @JMS\Accessor("getReferences")
     * @JMS\MaxDepth(3)
     */
    protected $_references = array();

    /**
     * Block constructor
     * @param string $title
     * @param string $content
     */
    public function __construct($title = null, $content = null)
    {
        $this->active = false;
        $this->images = new ArrayCollection();
        if (!is_null($title)) {
            $this->setTitle($title);
        }
        if (!is_null($content)) {
            $this->setContent($content);
        }
    }

    /**
     * Fill null collection properties
     *
     * @ORM\PostLoad
     */
    public function initialize()
    {
        if (is_null($this->images)) {
            $this->images = new ArrayCollection();
        }
    }

    /**
     * Get the value of id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id.
     *
     * @param string
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set the value of active.
     *
     * @param boolean $active
     * @return $this
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get the value of active.
     *
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Get the value of title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title.
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get the value of content.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the value of content.
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Get the value of type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the value of type.
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Remove a block image
     *
     * @param BlockImageInterface $image
     * @return $this
     */
    public function removeImage(BlockImageInterface $image)
    {
        $this->images->remove($image);

        return $this;
    }

    /**
     * Add a block image
     *
     * @param BlockImageInterface $image
     * @return $this
     */
    public function addImage(BlockImageInterface $image)
    {
        $image->setBlock($this);
        $this->images->add($image);

        return $this;
    }

    /**
     * Returns the block images
     *
     * @return ArrayCollection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Returns bool if the block has images
     *
     * @return ArrayCollection
     */
    public function hasImages()
    {
        return count($this->images) > 0;
    }

    /**
     * Returns first image
     *
     * @return BlockImageInterface
     */
    public function getFirstImage()
    {
        $image = '';
        if ($this->hasImages()) {
            $image = $this->images->get(0);
        }
        return $image;
    }

    /**
     * Returns first image url
     *
     * @return string
     */
    public function getFirstImageUrl()
    {
        $url = '';
        if ($image = $this->getFirstImage()) {
            $url = $image->getUrl();
        }
        return $url;
    }

    /**
     * Returns first image url
     *
     * @return string
     */
    public function getFirstImageName()
    {
        $url = '';
        if ($image = $this->getFirstImage()) {
            $url = $image->getName();
        }
        return $url;
    }

    /**
     * Returns the block images ID
     *
     * @return array
     */
    public function getImagesId()
    {
        $ids = array();
        foreach ($this->images as $image) {
            $ids[] = $image->getId();
        }
        return $ids;
    }

    /**
     * Get the value of template.
     *
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set the value of template.
     *
     * @param mixed $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * Get the value of page.
     *
     * @return PageInterface
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Get the value of page.
     *
     * @return string|null
     */
    public function getPageId()
    {
        return $this->getPage() ?
            $this->getPage()->getId() : null;
    }

    /**
     * Set the value of page.
     *
     * @param PageInterface $page
     * @return $this
     */
    public function setPage($page)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * Displays a _references param for JMS
     * with related entities data
     *
     * @param UserInterface|null $user
     * @param array              $groups
     * @return array
     */
    public function getReferences(UserInterface $user = null, $groups = array())
    {
        if (empty($this->_references)) {
            $this->_references = array(
                'images' => array()

            );
            foreach ($this->getImages() as $image) {
                $this->_references['images']{$image->getId()} = $image;
            }
        }
        return $this->_references;
    }

    public function __toString()
    {
        return (string) $this->getTitle();
    }

    /**
     * Get the value of plugin
     * @return mixed
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * Set the value of plugin
     * @param mixed $plugin
     * @return $this
     */
    public function setPlugin($plugin)
    {
        $this->plugin = $plugin;
        return $this;
    }
}
