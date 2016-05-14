<?php

namespace PhpInk\Nami\CoreBundle\Model\Odm;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Hateoas\Configuration\Annotation as Hateoas;
use PhpInk\Nami\CoreBundle\Model\Odm\Core;
use PhpInk\Nami\CoreBundle\Model\PageInterface;
use PhpInk\Nami\CoreBundle\Model\BlockInterface;
use PhpInk\Nami\CoreBundle\Model\CategoryInterface;
use PhpInk\Nami\CoreBundle\Model\Image\BackgroundInterface;
use PhpInk\Nami\CoreBundle\Model\UserInterface;

/**
 * Document\Page
 *
 * @ODM\Document(
 *     collection="pages",
 *     repositoryClass="PhpInk\Nami\CoreBundle\Repository\Odm\PageRepository"
 * )
 * @ODM\HasLifecycleCallbacks
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("custom", custom = {
 *     "id", "active", "title", "slug", "header",
 *     "content", "background", "category",
 *     "createdAt", "updatedAt", "createdBy", "updatedBy"
 * })
 * @Hateoas\Relation(
 *   "self",
 *   href = @Hateoas\Route(
 *     "nami_api_get_page",
 *     parameters = {"id" = "expr(object.getId())"}
 *   )
 * )
 */
class Page extends Core\Document implements PageInterface
{
    use Core\CreatedUpdatedAtTrait,
        Core\CreatedUpdatedByTrait;

    /**
     * Primary Key
     * @var string
     * @ODM\Id
     * @JMS\Expose
     */
    protected $id;

    /**
     * @var bool
     * @ODM\Boolean
     * @JMS\Expose
     * @JMS\Groups({"full"})
     */
    protected $active;

    /**
     * @var string
     * @ODM\String
     * @JMS\Expose
     */
    protected $title;

    /**
     * @var string
     * @ODM\String
     * Gedmo\Slug(handlers={
     *      Gedmo\SlugHandler(class="Gedmo\Sluggable\Handler\RelativeSlugHandler", options={
     *          Gedmo\SlugHandlerOption(name="relationField", value="category"),
     *          Gedmo\SlugHandlerOption(name="relationSlugField", value="slug"),
     *          Gedmo\SlugHandlerOption(name="separator", value="/")
     *      })
     * }, fields={"title"}, unique=true)
     * @JMS\Expose
     */
    private $slug;

    /**
     * @var string
     * @ODM\String
     * @JMS\Expose
     */
    protected $header;

    /**
     * @var string
     * @ODM\String
     * @JMS\Expose
     */
    protected $metaDescription;

    /**
     * @var string
     * @ODM\String
     * @JMS\Expose
     */
    protected $metaKeywords;

    /**
     * @var string
     * @ODM\String
     * @JMS\Expose
     */
    protected $content;

    /**
     * @var ArrayCollection<Block>
     * @ODM\ReferenceMany(targetDocument="Block", mappedBy="page", cascade={"persist", "remove"}, sort={"position": "asc"})
     * @JMS\Expose
     */
    protected $blocks;

    /**
     * @var Image
     * @ODM\ReferenceOne(targetDocument="PhpInk\Nami\CoreBundle\Model\Odm\Image\Background", cascade={"persist", "remove"})
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\Accessor("getBackgroundId")
     */
    protected $background;

    /**
     * @var Category
     * @ODM\ReferenceOne(targetDocument="Category", inversedBy="blocks")
     * @ODM\EmbedOne(targetDocument="Category")
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\Accessor("getCategoryId")
     */
    protected $category;

    /**
     * @var string
     * @ODM\String
     * @JMS\Expose
     */
    private $template;

    /**
     * @var string
     * @ODM\String
     * @JMS\Expose
     */
    protected $backgroundColor;

    /**
     * @var string
     * @ODM\String
     * @JMS\Expose
     */
    protected $borderColor;

    /**
     * @var string
     * @ODM\String
     * @JMS\Expose
     */
    protected $footerColor;

    /**
     * @var bool
     * @ODM\Boolean
     * @JMS\Expose
     */
    protected $negativeText;

    /**
     * @var array
     * @JMS\Expose
     * @JMS\Accessor("getReferences")
     * @JMS\MaxDepth(3)
     */
    protected $_references = array();

    /**
     * Page constructor
     */
    public function __construct()
    {
        $this->active = false;
        $this->negativeText = false;
        $this->blocks = new ArrayCollection();
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
     * Get the value of slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return $this
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get the value of header.
     *
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Set the value of header.
     *
     * @param string $header
     * @return $this
     */
    public function setHeader($header)
    {
        $this->header = $header;
        return $this;
    }

    /**
     * Get the value of metaKeywords.
     *
     * @return string
     */
    public function getMetaKeywords()
    {
        return $this->metaKeywords;
    }

    /**
     * Set the value of metaKeywords.
     *
     * @param string $keywords
     * @return $this
     */
    public function setMetaKeywords($keywords)
    {
        $this->metaKeywords = $keywords;
        return $this;
    }

    /**
     * Get the value of metaDescription.
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Set the value of metaDescription.
     *
     * @param string $description
     * @return $this
     */
    public function setMetaDescription($description)
    {
        $this->metaDescription = $description;
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
     * Remove a block block
     *
     * @param BlockInterface $block
     * @return $this
     */
    public function removeBlock(BlockInterface $block)
    {
        $this->blocks->remove($block);

        return $this;
    }

    /**
     * Add a block block
     *
     * @param BlockInterface $block
     * @return $this
     */
    public function addBlock(BlockInterface $block)
    {
        $block->setPage($this);
        $this->blocks->add($block);

        return $this;
    }

    /**
     * Returns the block blocks
     *
     * @return Collection
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * Add a block block
     *
     * @param Collection $blocks
     * @return $this
     */
    public function setBlocks(Collection $blocks)
    {
        foreach ($blocks as $block) {
            $block->setPage($this);
        }
        $this->blocks = $blocks;

        return $this;
    }

    /**
     * Get the value of template.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set the value of template.
     *
     * @param string $template
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
    }

    /**
     * Get the id of the page background.
     *
     * @return string
     */
    public function getBackgroundId()
    {
        return $this->background ?
        $this->background->getId() : null;
    }

    /**
     * Get the url of the page background.
     *
     * @return string
     */
    public function getBackgroundUrl()
    {
        return $this->background ?
            $this->background->getUrl() : '';
    }

    /**
     * Set background Page (one to one).
     *
     * @param BackgroundInterface $background
     * @return $this
     */
    public function setBackground(BackgroundInterface $background = null)
    {
        if ($background) {
            $background->setPage($this);
        }
        $this->background = $background;

        return $this;
    }

    /**
     * Get background (one to one).
     *
     * @return BackgroundInterface
     */
    public function getBackground()
    {
        return $this->background;
    }

    public function hasMultipleImages()
    {
        $hasMultipleImages = false;
        foreach ($this->getBlocks() as $block) {
            if (count($block->getImages()) > 1) {
                $hasMultipleImages = true;
                break;
            }
        }
        return $hasMultipleImages;
    }

    /**
     * Get the id of the category page.
     *
     * @return string
     */
    public function getCategoryId()
    {
        return $this->category ?
            $this->category->getId() : null;
    }

    /**
     * Set category Page (one to one).
     *
     * @param CategoryInterface $category
     * @return $this
     */
    public function setCategory(CategoryInterface $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category Page (one to one).
     *
     * @return $this
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Get the value of backgroundColor.
     *
     * @return string
     */
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    /**
     * Set the value of backgroundColor.
     *
     * @param string $backgroundColor
     * @return $this
     */
    public function setBackgroundColor($backgroundColor)
    {
        $this->backgroundColor = $backgroundColor;
        return $this;
    }

    /**
     * Get the value of borderColor.
     *
     * @return string
     */
    public function getBorderColor()
    {
        return $this->borderColor;
    }

    /**
     * Set the value of borderColor.
     *
     * @param string $borderColor
     * @return $this
     */
    public function setBorderColor($borderColor)
    {
        $this->borderColor = $borderColor;
        return $this;
    }

    /**
     * Get the value of footerColor.
     *
     * @return string
     */
    public function getFooterColor()
    {
        return $this->footerColor;
    }

    /**
     * Set the value of footerColor.
     *
     * @param string $footerColor
     * @return $this
     */
    public function setFooterColor($footerColor)
    {
        $this->footerColor = $footerColor;
        return $this;
    }

    /**
     * Get the value of negativeText.
     *
     * @return boolean
     */
    public function isNegativeText()
    {
        return $this->negativeText;
    }

    /**
     * Set the value of negativeText.
     *
     * @param boolean $negativeText
     * @return $this
     */
    public function setNegativeText($negativeText)
    {
        $this->negativeText = $negativeText;
        return $this;
    }

    /**
     * Displays a _references param for JMS
     * with related entities data
     *
     * @param UserInterface|null $user
     * @param array     $groups
     * @return array
     */
    public function getReferences(UserInterface $user = null, $groups = array())
    {
        if (empty($this->_references)) {
            $this->_references = array(
                'category' => $this->getCategory(),
                'background' => $this->getBackground()

            );
            if ($user && $user->isAdmin()) {
                $this->_references['createdBy'] = $this->getCreatedBy();
                $this->_references['updatedBy'] = $this->getUpdatedBy();
            }
        }
        return $this->_references;
    }

    public function __toString()
    {
        return (string) $this->getSlug();
    }
}
