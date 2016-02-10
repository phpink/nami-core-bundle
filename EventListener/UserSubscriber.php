<?php

namespace PhpInk\Nami\CoreBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use PhpInk\Nami\CoreBundle\Security\UserProvider;
use PhpInk\Nami\CoreBundle\Model\UserInterface;
use \Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * Doctrine listener updating the username and password fields.
 */
class UserSubscriber implements EventSubscriber
{
    /**
     * @var UserProvider
     */
    private $userProvider;

    /**
     * Constructor
     *
     * @param UserProvider $userProvider
     */
    public function __construct(UserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }

    /**
     * Pre persist listener based on doctrine commons, overwrite for drivers
     * that are not compatible with the commons events.
     *
     * @param LifecycleEventArgs $args weak typed to allow overwriting
     */
    public function prePersist(LifecycleEventArgs  $args)
    {
        $object = $args->getObject();
        if ($object instanceof UserInterface) {
            $this->updateUserFields($object);
        }
    }

    /**
     * Pre update listener based on doctrine commons, overwrite to update
     * the changeset in the UoW and to handle non-common event argument
     * class.
     *
     * @param LifecycleEventArgs $args weak typed to allow overwriting
     */
    public function preUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if ($object instanceof UserInterface) {
            $this->updateUserFields($object);
        }
    }

    /**
     * This must be called on prePersist and preUpdate if the event is about a
     * user.
     *
     * @param UserInterface $user
     */
    protected function updateUserFields(UserInterface $user)
    {
        $this->userProvider->updateFields($user);
    }

    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'preUpdate'
        );
    }
}
