<?php

namespace PhpInk\Nami\CoreBundle\Util;

interface TokenGeneratorInterface
{
    /**
     * @return string
     */
    public function generateToken();
}
