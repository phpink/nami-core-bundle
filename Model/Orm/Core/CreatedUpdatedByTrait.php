<?php

namespace PhpInk\Nami\CoreBundle\Model\Orm\Core;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as JMS;
use PhpInk\Nami\CoreBundle\Model\Orm\User;

/**
 * Record creation & update
 * authors of Doctrine entites
 */
trait CreatedUpdatedByTrait
{
    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="PhpInk\Nami\CoreBundle\Model\Orm\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * @JMS\Expose
     * @JMS\Type("integer")
     * @JMS\Accessor("getCreatedById")
     * @JMS\Groups({"full"})
     */
    protected $createdBy;

    /**
     * @Gedmo\Blameable(on="update")
     * @ORM\ManyToOne(targetEntity="PhpInk\Nami\CoreBundle\Model\Orm\User")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     * @JMS\Expose
     * @JMS\Type("integer")
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
     * @return int|null
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
     * @return int|null
     */
    public function getUpdatedById()
    {
        return $this->updatedBy ?
            $this->updatedBy->getId() : null;
    }
}
