<?php

namespace PhpInk\Nami\CoreBundle\Repository\Odm;

use PhpInk\Nami\CoreBundle\Repository\OdmRepository;

class ConfigurationRepository extends OdmRepository
{
    protected $orderByFields = array(
        'id' => 'id',
        'name' => 'name',
        'value' => 'value'
    );

    /**
     * Get configuration values
     * @param $names
     * @return mixed
     */
    public function getValues($names)
    {
        $query = $this->createQueryBuilder('this');
        $query->select('name', 'value')
            ->field('name')->in($names)
            ->sort('name', 'asc');
        $valuesRaw = $query->getQuery()->toArray();
        $values = array();
        foreach ($valuesRaw as $param) {
            $name = $param->getName();
            $values[$name] = $param->getValue();
        }
        return $values;
    }
}
