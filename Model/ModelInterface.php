<?php

namespace PhpInk\Nami\CoreBundle\Model;

/**
 * Model interface for ORM, ODM
 */
interface ModelInterface
{
    /**
     * Primary Key getter
     * @return int
     */
    public function getId();
}
