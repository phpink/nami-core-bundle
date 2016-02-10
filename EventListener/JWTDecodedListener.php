<?php

namespace PhpInk\Nami\CoreBundle\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTDecodedEvent;
use PhpInk\Nami\CoreBundle\Exception\TokenExpiredException;
use PhpInk\Nami\CoreBundle\Exception\TokenNotValidException;

/**
 * Class JWTDecodedListener
 * @package PhpInk\Nami\CoreBundle\EventListener
 */
class JWTDecodedListener
{
    /**
     * @param JWTDecodedEvent $event
     * @throws TokenExpiredException when token has expired
     * @throws TokenNotValidException when token matched no user
     */
    public function onJWTDecodedResponse(JWTDecodedEvent $event)
    {
        if (!$event->isValid()) {
            throw new TokenExpiredException();
        }
    }
}
