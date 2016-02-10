<?php

namespace PhpInk\Nami\CoreBundle\Model\Odm\Core;

use PhpInk\Nami\CoreBundle\Model\ModelInterface;

/**
 * Base Document for the bundle
 * For reflection purposes (instanceof, get_class)
 */
abstract class Document implements ModelInterface
{
    /**
     * Primary Key getter
     * @return int
     */
    abstract public function getId();
}
