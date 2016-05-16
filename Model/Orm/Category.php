<?php

namespace PhpInk\Nami\CoreBundle\Model\Orm;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Hateoas\Configuration\Annotation as Hateoas;
use PhpInk\Nami\CoreBundle\Model\CategoryInterface;
use PhpInk\Nami\CoreBundle\Model\Orm\Block;
use PhpInk\Nami\CoreBundle\Model\Orm\Page;

/**
 * Entity\Category
 *
 * @ORM\Entity(repositoryClass="PhpInk\Nami\CoreBundle\Repository\Orm\CategoryRepository")
 * @ORM\Table(
 *     name="category"
 * )
 * @ORM\HasLifecycleCallbacks
 *
 * @Gedmo\Tree(type="materializedPath")
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("custom", custom = {
 *     "id", "active", "parent",
 *     "locales", "items",
 *     "createdAt", "updatedAt", "createdBy", "updatedBy"
 * })
 * @Hateoas\Relation(
 *   "self",
 *   href = @Hateoas\Route(
 *     "nami_api_get_category",
 *     parameters = {"id" = "expr(object.getId())"}
 *   )
 * )
 */
class Category extends Core\Entity implements CategoryInterface
{
    use Core\CreatedUpdatedAtTrait,
        Core\CreatedUpdatedByTrait;

    /**
     * Primary Key
     * @var int
     * @Gedmo\TreePathSource
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
     * @var int
     * @Gedmo\TreeLevel
     * @ORM\Column(type="integer")
     */
    protected $level = 0;

    /**
     * @var string
     * @Gedmo\TreePath
     * @ORM\Column(name="path", type="string", length=3000, nullable=true)
     */
    private $path;

    /**
     * @var Category
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     * @JMS\Expose
     * @JMS\Type("integer")
     * @JMS\Accessor("getParentId")
     */
    protected $parent;

    /**
     * @var string
     * @Gedmo\TreePathSource
     * @ORM\Column(name="name", type="string", length=255)
     * @JMS\Expose
     */
    private $name;

    /**
     * @var string
     * @Gedmo\Slug(handlers={
     *      @Gedmo\SlugHandler(class="Gedmo\Sluggable\Handler\TreeSlugHandler", options={
     *          @Gedmo\SlugHandlerOption(name="parentRelationField", value="parent"),
     *          @Gedmo\SlugHandlerOption(name="separator", value="/")
     *      })
     * }, fields={"name"})
     * @ORM\Column(name="slug", type="string", length=255)
     * @JMS\Expose
     */
    private $slug;

    /**
     * @var string
     * @ORM\Column(name="title", type="string", length=255)
     * @JMS\Expose
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(name="header", type="string", length=255)
     * @JMS\Expose
     */
    protected $header;

    /**
     * @var string
     * @ORM\Column(name="meta_description", type="string", length=255)
     * @JMS\Expose
     */
    protected $metaDescription;

    /**
     * @var string
     * @ORM\Column(name="meta_keywords", type="string", length=255)
     * @JMS\Expose
     */
    protected $metaKeywords;

    /**
     * @var string
     * @ORM\Column(name="content", type="text")
     * @JMS\Expose
     */
    protected $content;

    /**
     * @var Collection<Category>
     * @JMS\Expose
     * @JMS\Type("array<PhpInk\Nami\CoreBundle\Model\Orm\Category>")
     * @JMS\Groups({"standard", "full"})
     */
    protected $items;

    /**
     * @var ArrayCollection<Page>
     * @ORM\OneToMany(targetEntity="Page",
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
     * @ORM\PostLoad
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
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id.
     *
     * @param integer
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
     * @return integer
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
     * @return Category
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
