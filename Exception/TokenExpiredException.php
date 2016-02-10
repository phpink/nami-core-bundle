<?php

namespace PhpInk\Nami\CoreBundle\Exception;

class TokenExpiredException extends \Exception
{
    public $message = 'Token has expired.';
    public $code = 401;
}
