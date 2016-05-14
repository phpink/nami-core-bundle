<?php

namespace PhpInk\Nami\CoreBundle\Model\Orm\Image;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Gedmo\Mapping\Annotation as Gedmo;
use PhpInk\Nami\CoreBundle\Model\Orm\Core;
use PhpInk\Nami\CoreBundle\Model\Image\UserImageInterface;
use PhpInk\Nami\CoreBundle\Model\Orm\Image;
use PhpInk\Nami\CoreBundle\Model\Orm\User;
use PhpInk\Nami\CoreBundle\Model\UserInterface;

/**
 * Entity\Image
 *
 * @ORM\Entity(repositoryClass="PhpInk\Nami\CoreBundle\Repository\Orm\Image\Image\UserImageRepository")
 * @ORM\Table(name="user_image")
 * @ORM\HasLifecycleCallbacks()
 * @JMS\ExclusionPolicy("all")
 */
class UserImage extends Image implements UserImageInterface
{
    use Core\CreatedUpdatedAtTrait,
        Core\CreatedUpdatedByTrait;
    
    const DEFAULT_SUBFOLDER = 'user';

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="PhpInk\Nami\CoreBundle\Model\Orm\User", inversedBy="avatar")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    protected $user;

    /**
     * Set user.
     *
     * @param UserInterface $user
     * @return $this
     */
    public function setUser(UserInterface $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }
}
