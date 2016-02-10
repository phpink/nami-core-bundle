<?php

namespace PhpInk\Nami\CoreBundle\Model\Orm;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Entity\Configuration
 *
 * @ORM\Entity(repositoryClass="PhpInk\Nami\CoreBundle\Repository\Orm\ConfigurationRepository")
 * @ORM\Table(name="configuration")
 * @JMS\ExclusionPolicy("all")
 */
class Configuration extends Core\Entity
{

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
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     * @JMS\Expose
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose
     */
    protected $value;

    public function __construct($name = null, $value = null)
    {
        if (!is_null($name)) {
            $this->setName($name);
        }
        if (!is_null($value)) {
            $this->setValue($value);
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
     * @return Configuration
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set the value of name.
     *
     * @param string $name
     * @return Configuration
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of value.
     *
     * @param string $value
     * @return Configuration
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the value of value.
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
