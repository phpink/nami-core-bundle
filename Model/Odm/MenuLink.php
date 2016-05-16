<?php

namespace PhpInk\Nami\CoreBundle\Model\Odm;

use PhpInk\Nami\CoreBundle\Model\MenuLinkInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use PhpInk\Nami\CoreBundle\Model\Odm\Core;

/**
 * Document\MenuLink
 *
 * @ODM\Document(
 *     collection="menu",
 *     repositoryClass="PhpInk\Nami\CoreBundle\Repository\Odm\MenuRepository"
 * )
 * @ODM\HasLifecycleCallbacks
 *
 * @Gedmo\Tree(type="materializedPath")
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("custom", custom = {
 *     "id", "active", "parent", "position",
 *     "items", "createdAt", "updatedAt", "createdBy", "updatedBy"
 * })
 */
class MenuLink extends Core\Document implements MenuLinkInterface
{
    use Core\SortableItemTrait;

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
     * @var MenuLink
     * @Gedmo\TreeParent
     * @ODM\ReferenceOne(targetDocument="MenuLink")
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\Accessor("getParentId")
     */
    protected $parent;

    /**
     * @var string
     * @ODM\String
     */
    private $name;

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
    protected $link;

    /**
     * @var Collection<MenuLink>
     * @JMS\Expose
     * @JMS\Type("array<PhpInk\Nami\CoreBundle\Model\Odm\MenuLink>")
     * @JMS\Groups({"standard", "full"})
     */
    protected $items;

    /**
     * Menu constructor
     */
    public function __construct()
    {
        $this->active = false;
        $this->items = new ArrayCollection();
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
     * Get the id of the parent Menu.
     *
     * @return string
     */
    public function getParentId()
    {
        return $this->parent ?
        $this->parent->getId() : null;
    }

    /**
     * Set parent Menu (one to one).
     *
     * @param MenuLinkInterface $parent
     * @return $this
     */
    public function setParent(MenuLinkInterface $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent Menu (one to one).
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
     * Get the value of link.
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set the value of link.
     *
     * @param string $link
     * @return $this
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    public function addItem(MenuLinkInterface $Menu)
    {
        $this->items[] = $Menu;

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

    public function __toString()
    {
        return (string) $this->getId();
    }
}
