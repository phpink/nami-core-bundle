<?php

namespace PhpInk\Nami\CoreBundle\Model\Orm\Core;

use PhpInk\Nami\CoreBundle\Model\ModelInterface;

/**
 * Base Entity for the bundle
 * For reflection purposes (instanceof, get_class)
 */
abstract class Entity implements ModelInterface
{
    /**
     * Primary Key getter
     * @return int
     */
    abstract public function getId();
}
