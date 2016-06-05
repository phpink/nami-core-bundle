<?php

namespace PhpInk\Nami\CoreBundle\Model\Orm;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use Hateoas\Configuration\Annotation as Hateoas;
use PhpInk\Nami\CoreBundle\Model\MenuLinkInterface;

/**
 * Entity\Menu
 *
 * @ORM\Entity(repositoryClass="PhpInk\Nami\CoreBundle\Repository\Orm\MenuRepository")
 * @ORM\Table(
 *     name="menu"
 * )
 * @ORM\HasLifecycleCallbacks
 *
 * @Gedmo\Tree(type="materializedPath")
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("custom", custom = {
 *     "id", "active", "parent", "position",
 *     "items", "createdAt", "updatedAt", "createdBy", "updatedBy"
 * })
 * @Hateoas\Relation(
 *   "self",
 *   href = @Hateoas\Route(
 *     "nami_api_get_menu",
 *     parameters = {"id" = "expr(object.getId())"}
 *   )
 * )
 */
class MenuLink extends Core\Entity implements MenuLinkInterface
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
     * @var MenuLink
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="MenuLink")
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
     * @ORM\Column(name="title", type="string", length=255)
     * @JMS\Expose
     */
    protected $title;

    /**
     * @var string
     * @ORM\Column(name="link", type="string", length=255)
     * @JMS\Expose
     */
    protected $link;

    /**
     * @var Collection<Menu>
     * @JMS\Expose
     * @JMS\Type("array<PhpInk\Nami\CoreBundle\Model\Orm\MenuLink>")
     * @JMS\Groups({"tree"})
     */
    protected $items;

    /**
     * @var integer
     * @Gedmo\Sortable(groups={"parent"})
     * @ORM\Column(name="position", type="integer")
     * @JMS\Expose
     */
    private $position = 0;

    /**
     * Menu constructor
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
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
     * Get the id of the parent Menu.
     *
     * @return integer
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

    public function addItem(MenuLinkInterface $menu)
    {
        $this->items[] = $menu;

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

    public function __toString()
    {
        return (string) $this->getId();
    }
}
