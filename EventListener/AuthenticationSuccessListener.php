<?php

namespace PhpInk\Nami\CoreBundle\EventListener;

use Doctrine\ODM\MongoDB\DocumentManager;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use PhpInk\Nami\CoreBundle\Model\UserInterface;
use PhpInk\Nami\CoreBundle\Model\Orm\Analytics\LoginAnalytics as OrmLoginAnalytics;
use PhpInk\Nami\CoreBundle\Model\Odm\Analytics\LoginAnalytics as OdmLoginAnalytics;

/**
 * Class AuthenticationSuccessListener
 * @package PhpInk\Nami\CoreBundle\EventListener
 */
class AuthenticationSuccessListener
{
    /**
     * @var DocumentManager|EntityManager
     */
    protected $em;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
    /**
     * Adding extra user data to the JSON Web Token
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if ($user instanceof UserInterface) {
            $this->updateUser($event, $user);

            $context = SerializationContext::create();
            $context->setGroups(array('Default', 'full', 'userFull'));
            $data['user'] = json_decode($this->serializer->serialize($user, 'json', $context));
            $event->setData($data);
        }
        return;
    }

    public function updateUser(AuthenticationSuccessEvent $event, UserInterface $user)
    {
        // Update user last login
        $user->setLastLogin(new \DateTime());
        if ($user->getIp() !== $event->getRequest()->getClientIp()) {
            $user->setIp($event->getRequest()->getClientIp());
        }
        $this->em->persist($user);
        $this->em->flush();

        // Register a new login hit
        $userAgent = $event->getRequest()->headers->get('user-agent');
        if ($this->em instanceof DocumentManager) {
            $newHit = new OdmLoginAnalytics($user, $userAgent);
        } else {
            $newHit = new OrmLoginAnalytics($user, $userAgent);
        }
        $this->em->persist($newHit);
        $this->em->flush();
    }

    /**
     * @var DocumentManager|EntityManager $em
     */
    public function setManager($em)
    {
        $this->em = $em;
    }

}
