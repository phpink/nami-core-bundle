<?php

namespace PhpInk\Nami\CoreBundle\Exception;

class TokenNotValidException extends \Exception
{
    public $message = 'Token is not valid.';
    public $code = 401;
}
