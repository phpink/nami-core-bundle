<?php

namespace PhpInk\Nami\CoreBundle\Model\Odm\Image;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use JMS\Serializer\Annotation as JMS;
use PhpInk\Nami\CoreBundle\Model\Odm\User;
use PhpInk\Nami\CoreBundle\Model\Orm\Core;
use PhpInk\Nami\CoreBundle\Model\Image\UserImageInterface;
use PhpInk\Nami\CoreBundle\Model\Odm\Image;
use PhpInk\Nami\CoreBundle\Model\UserInterface;

/**
 * Document\Image
 *
 * @ODM\Document(
 *     collection="user_images",
 *     repositoryClass="PhpInk\Nami\CoreBundle\Repository\Odm\Image\UserImageRepository"
 * )
 * @ODM\HasLifecycleCallbacks
 * @JMS\ExclusionPolicy("all")
 */
class UserImage extends Image implements UserImageInterface
{
    use Core\CreatedUpdatedAtTrait,
        Core\CreatedUpdatedByTrait;
    
    const DEFAULT_SUBFOLDER = 'user';

    /**
     * @var User
     * @ODM\ReferenceOne(targetDocument="PhpInk\Nami\CoreBundle\Model\Odm\User", inversedBy="images")
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
