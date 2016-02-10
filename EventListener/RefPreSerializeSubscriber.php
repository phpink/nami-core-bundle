<?php

namespace PhpInk\Nami\CoreBundle\EventListener;

use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use PhpInk\Nami\CoreBundle\Model\UserInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

/**
 * JMS PreSerialize listener
 * to call entities.getReferences
 * with the security context
 */
class RefPreSerializeSubscriber implements EventSubscriberInterface
{
    /**
     * @var SecurityContextInterface|TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var UserInterface|null
     */
    private $user;

    /**
     * Constructor
     *
     * @param $tokenStorage
     */
    public function __construct($tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
        try {
            if ($this->tokenStorage) {
                $token = $this->tokenStorage->getToken();
                if ($token) {
                    $this->user = $token->getUser();
                }
            }
        } catch (AuthenticationCredentialsNotFoundException $e) {}
    }

    /**
     * {@inheritDoc}
     */
    public function onPreSerialize(PreSerializeEvent $event)
    {
        $object = $event->getObject();
        if (method_exists($object, 'getReferences')) {
            $object->getReferences(
                $this->user,
                $event->getContext()
                      ->attributes
                      ->get('groups')
                      ->get('value')
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize'),
        );
    }
}
