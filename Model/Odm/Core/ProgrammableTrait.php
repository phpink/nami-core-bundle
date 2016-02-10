<?php

namespace PhpInk\Nami\CoreBundle\Model\Odm\Core;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use JMS\Serializer\Annotation as JMS;

/**
 * Adds dateStart & dateEnd properties
 * to program entity display on a given period
 */
trait ProgrammableTrait
{
    /**
     * @ODM\Date
     * @JMS\Expose
     * @JMS\Groups({"full"})
     */
    protected $dateStart;

    /**
     * @ODM\Date
     * @JMS\Expose
     * @JMS\Groups({"full"})
     */
    protected $dateEnd;

    /**
     * Is the programmation active ?
     *
     * @return boolean
     */
    public function isActive()
    {
        return (
            $this->getDateStart()->getTimestamp() <= time() &&
            $this->getDateEnd()->getTimestamp() >= time()
        );
    }

    /**
     * Set dateStart
     *
     * @param \DateTime $dateStart
     * @return self
     */
    public function setDateStart($dateStart)
    {
        if (!$dateStart instanceof \DateTime) {
            $dateStart = new \DateTime($dateStart);
        }
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * Get dateStart
     *
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Set dateEnd
     *
     * @param \DateTime dateEnd
     * @return self
     */
    public function setDateEnd($dateEnd)
    {
        if (!$dateEnd instanceof \DateTime) {
            $dateEnd = new \DateTime($dateEnd);
        }
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd
     *
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }
}
