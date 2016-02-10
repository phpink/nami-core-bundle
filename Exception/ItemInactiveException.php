<?php

namespace PhpInk\Nami\CoreBundle\Exception;

class ItemInactiveException extends \Exception
{
    public $message = 'Item is inactive.';
    public $code = 401;
}
