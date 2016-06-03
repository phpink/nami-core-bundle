<?php

namespace PhpInk\Nami\CoreBundle\Model\Orm\Core;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Locale Doctrine relation
 * for an **_Locale Entity
 */
trait SortableItemTrait
{
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
