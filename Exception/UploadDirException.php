<?php

namespace PhpInk\Nami\CoreBundle\Exception;

class UploadDirException extends \Exception
{
    public $message = 'The application upload directory is not accessible.';
    public $code = 500;
}
