<?php

namespace PhpInk\Nami\CoreBundle\Model\Odm;

use PhpInk\Nami\CoreBundle\Model\CategoryInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use PhpInk\Nami\CoreBundle\Model\Odm\Core;

/**
 * Document\Category
 *
 * @ODM\Document(
 *     collection="categories",
 *     repositoryClass="PhpInk\Nami\CoreBundle\Repository\Odm\CategoryRepository"
 * )
 * @ODM\HasLifecycleCallbacks
 *
 * @Gedmo\Tree(type="materializedPath")
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("custom", custom = {
 *     "id", "active", "parent",
 *     "locales", "items",
 *     "createdAt", "updatedAt", "createdBy", "updatedBy"
 * })
 */
class Category extends Core\Document implements CategoryInterface
{
    use Core\CreatedUpdatedAtTrait,
        Core\CreatedUpdatedByTrait;

    /**
     * Primary Key
     * @var string
     * @Gedmo\TreePathSource
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
     * @var int
     * @Gedmo\TreeLevel
     * @ODM\Int
     */
    protected $level = 0;

    /**
     * @var string
     * @Gedmo\TreePath
     * @ODM\String
     */
    private $path;

    /**
     * @var Category
     * @Gedmo\TreeParent
     * @ODM\ReferenceOne(targetDocument="Category")
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\Accessor("getParentId")
     */
    protected $parent;

    /**
     * @var string
     * @ODM\String
     * @JMS\Expose
     */
    private $name;

    /**
     * @var string
     * @ODM\String
     * @Gedmo\Slug(handlers={
     *      @Gedmo\SlugHandler(class="Gedmo\Sluggable\Handler\TreeSlugHandler", options={
     *          @Gedmo\SlugHandlerOption(name="parentRelationField", value="parent"),
     *          @Gedmo\SlugHandlerOption(name="separator", value="/")
     *      })
     * }, fields={"name"}, unique=true)
     * @JMS\Expose
     */
    private $slug;

    /**
     * @var string
     * @ODM\String
     * @JMS\Expose
     */
    protected $title;

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
     * @var Collection<Category>
     * @JMS\Expose
     * @JMS\Type("array<PhpInk\Nami\CoreBundle\Model\Odm\Category>")
     * @JMS\Groups({"standard", "full"})
     */
    protected $items;

    /**
     * @var ArrayCollection<Page>
     * @ODM\ReferenceMany(targetDocument="Page",
     * mappedBy="category", orphanRemoval=true,
     * cascade={"persist", "remove"})
     */
    protected $pages;


    /**
     * Category constructor
     */
    public function __construct()
    {
        $this->active = false;
        $this->items = new ArrayCollection();
        $this->pages = new ArrayCollection();
    }

    /**
     * Fill null collection properties
     *
     * @ODM\PostLoad
     */
    public function initialize()
    {
        if (is_null($this->items)) {
            $this->items = new ArrayCollection();
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
     * Unused:
     * Generated by Gedmo MaterializedPath
     * @return $this
     */
    public function updateLevel()
    {
        $level = 0;
        $parent = $this->parent;
        while ($parent) {
            $level++;
            $parent = $parent->getParent();
        }
        $this->level = $level;
        return $this;
    }

    /**
     * Get the value of level.
     * TreeLevel generated by Gedmo MaterializedPath
     *
     * @return integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Get the value of path.
     * TreePath generated by Gedmo MaterializedPath
     *
     * @return boolean
     */
    public function getPath()
    {
        return $this->path;
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
     * Get the id of the parent category.
     *
     * @return string
     */
    public function getParentId()
    {
        return $this->parent ?
        $this->parent->getId() : null;
    }

    /**
     * Set parent Category (one to one).
     *
     * @param CategoryInterface $parent
     * @return $this
     */
    public function setParent(CategoryInterface $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent Category (one to one).
     *
     * @return $this
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * Get the value of slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
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

    public function addItem(CategoryInterface $category)
    {
        $this->items[] = $category;

        return $this;
    }

    public function setItems($items)
    {
        if (is_array($items) || $items instanceof \Iterator) {
            foreach ($items as $item) {
                $item->setParent($this);
            }
            $this->items = $items;
        }
        return $this;
    }

    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return bool
     */
    public function hasPages()
    {
        $pageCount = $this->pages->count();
        return $pageCount > 0;
    }

    /**
     * @return ArrayCollection
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param ArrayCollection $pages
     */
    public function setPages($pages)
    {
        $this->pages = $pages;
    }

    public function generatePage()
    {
        $block = new Block(
            $this->getHeader(),
            $this->getContent()
        );

        $page = new Page();
        $page->setTitle($this->getName())
            ->setSlug($this->getSlug())
            ->setHeader($this->getHeader())
            ->setMetaKeywords($this->getMetaKeywords())
            ->setMetaDescription($this->getMetaDescription())
            ->addBlock($block);

        return $page;
    }

    public function __toString()
    {
        return (string) $this->getId();
    }
}
