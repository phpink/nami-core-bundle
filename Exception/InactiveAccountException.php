<?php

namespace PhpInk\Nami\CoreBundle\Exception;

class InactiveAccountException extends \Exception
{
    public $message = 'The account is not active.';
    public $code = 401;
}
