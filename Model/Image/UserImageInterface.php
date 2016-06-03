<?php

namespace PhpInk\Nami\CoreBundle\Model\Image;

use PhpInk\Nami\CoreBundle\Model\ImageInterface;
use PhpInk\Nami\CoreBundle\Model\UserInterface;

/**
 * Image interface
 */
interface UserImageInterface extends ImageInterface
{

    /**
     * Set user.
     *
     * @param UserInterface $user
     * @return $this
     */
    public function setUser(UserInterface $user);

    /**
     * Get user.
     *
     * @return UserInterface
     */
    public function getUser();
}
