<?php

namespace PhpInk\Nami\CoreBundle\Model\Odm;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use JMS\Serializer\Annotation as JMS;
use PhpInk\Nami\CoreBundle\Model\Odm\Core;

/**
 * Document\Configuration
 *
 * @ODM\Document(
 *     collection="configurations",
 *     repositoryClass="PhpInk\Nami\CoreBundle\Repository\Odm\ConfigurationRepository"
 * )
 * @JMS\ExclusionPolicy("all")
 */
class Configuration extends Core\Document
{

    /**
     * Primary Key
     * @var string
     * @ODM\Id
     * @JMS\Expose
     */
    protected $id;

    /**
     * @var string
     * @ODM\String
     * @JMS\Expose
     */
    protected $name;

    /**
     * @var string
     * @ODM\String
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
