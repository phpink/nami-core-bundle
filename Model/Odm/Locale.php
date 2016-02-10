<?php

namespace PhpInk\Nami\CoreBundle\Model\Odm;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use JMS\Serializer\Annotation as JMS;
use Hateoas\Configuration\Annotation as Hateoas;
use PhpInk\Nami\CoreBundle\Model\Odm\Core;

/**
 * Document\Locale
 *
 * @ODM\Document(
 *     collection="locales",
 *     repositoryClass="PhpInk\Nami\CoreBundle\Repository\LocaleRepository"
 * )
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessorOrder("custom", custom = {
 *     "id", "code", "locales"
 * })
 * @Hateoas\Relation(
 *   "self",
 *   href = @Hateoas\Route(
 *     "nami_api_get_locale",
 *     parameters = {"id" = "expr(object.getId())"}
 *   )
 * )
 */
class Locale extends Core\Document
{
    use Core\LocalizedTrait;

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
     * @ODM\Index(unique=true)
     * @JMS\Expose
     */
    protected $code;

    /**
     * Locale constructor
     *
     * @param string $code Locale code
     */
    public function __construct($code = null)
    {
        if (!is_null($code)) {
            $this->setCode($code);
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
     * @return Locale
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Set the value of code.
     *
     * @param string $code
     * @return string
     */
    public function setCode($code)
    {
        $this->code = strtolower($code);

        return $this;
    }

    /**
     * Get the value of code.
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    public function __toString()
    {
        return (string) $this->getCode();
    }
}
