<?php

namespace PhpInk\Nami\CoreBundle\Repository\Odm;

use PhpInk\Nami\CoreBundle\Repository\Odm\AbstractRepository as OdmRepository;
use PhpInk\Nami\CoreBundle\Repository\Core\ConfigurationRepositoryInterface;

class ConfigurationRepository extends OdmRepository implements ConfigurationRepositoryInterface
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
        /** @var \Doctrine\ODM\MongoDB\Query\Builder $query */
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
