<?php

namespace PhpInk\Nami\CoreBundle\Model\Odm\Core;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use PhpInk\Nami\CoreBundle\Model\Odm\User;

/**
 * Record creation & update
 * authors of Doctrine entites
 */
trait CreatedUpdatedByTrait
{
    /**
     * @Gedmo\Blameable(on="create")
     * @ODM\ReferenceOne(targetDocument="PhpInk\Nami\CoreBundle\Model\Odm\User")
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\Accessor("getCreatedById")
     * @JMS\Groups({"full"})
     */
    protected $createdBy;

    /**
     * @Gedmo\Blameable(on="update")
     * @ODM\ReferenceOne(targetDocument="PhpInk\Nami\CoreBundle\Model\Odm\User")
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\Accessor("getUpdatedById")
     * @JMS\Groups({"full"})
     */
    protected $updatedBy;

    /**
     * Set User entity related by `createdBy` (many to one).
     *
     * @param User $createdBy
     * @return self
     */
    public function setCreatedBy(User $createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get User entity related by `createdBy` (many to one).
     *
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Get createdBy User ID.
     *
     * @return string|null
     */
    public function getCreatedById()
    {
        return $this->createdBy ?
            $this->createdBy->getId() : null;
    }

    /**
     * Set User entity related by `updatedBy` (many to one).
     *
     * @param User $updatedBy
     * @return self
     */
    public function setUpdatedBy(User $updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get User entity related by `updatedBy` (many to one).
     *
     * @return User
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * Get updatedBy User ID.
     *
     * @return string|null
     */
    public function getUpdatedById()
    {
        return $this->updatedBy ?
            $this->updatedBy->getId() : null;
    }
}
