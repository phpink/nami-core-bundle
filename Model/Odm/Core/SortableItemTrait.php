<?php

namespace PhpInk\Nami\CoreBundle\Model\Odm\Core;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use JMS\Serializer\Annotation as JMS;

/**
 * Locale Doctrine relation
 * for an **_Locale Document
 */
trait SortableItemTrait
{

    /**
     * @var integer
     * @ODM\Int
     * @ODM\Index
     * @JMS\Expose
     */
    private $position = 0;

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return self
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }
}
