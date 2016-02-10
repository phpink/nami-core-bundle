<?php

namespace PhpInk\Nami\CoreBundle\Exception;

class LogicException extends \Exception
{
    public $message = 'Application logic error.';
    public $code = 500;
}
