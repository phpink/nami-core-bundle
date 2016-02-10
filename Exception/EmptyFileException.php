<?php

namespace PhpInk\Nami\CoreBundle\Exception;

class EmptyFileException extends \Exception
{
    public $message = 'The file to upload has not been sent.';
    public $code = 422;
}

