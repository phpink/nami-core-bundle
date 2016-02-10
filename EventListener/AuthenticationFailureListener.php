<?php

namespace PhpInk\Nami\CoreBundle\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;

/**
 * Class AuthenticationFailureListener
 * @package PhpInk\Nami\CoreBundle\EventListener
 */
class AuthenticationFailureListener
{
    /**
     * Update the response with the correct message
     * For non-active user (locked, reseller, with no subscription)
     *
     * @param AuthenticationFailureEvent $event
     *
     * @return AuthenticationFailureEvent
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {
        return $event;
    }
}
