<?php

namespace PhpInk\Nami\CoreBundle\Repository\Orm;

use Doctrine\ORM\AbstractQuery;
use PhpInk\Nami\CoreBundle\Repository\OrmRepository;

class ConfigurationRepository extends OrmRepository
{
    protected $orderByFields = array(
        'id' => 'this.id',
        'name' => 'this.name',
        'value' => 'this.value'
    );

    /**
     * Get configuration values
     * @param $names
     * @return mixed
     */
    public function getValues($names)
    {
        $query = $this->createQueryBuilder('this');
        $query->select(
            array('this.name', 'this.value')
        )->where(
            $query->expr()->in(
                'this.name', $names
            )
        )->orderBy('this.name', 'asc');
        $valuesRaw = $query->getQuery()->getResult(
            AbstractQuery::HYDRATE_ARRAY
        );
        $values = array();
        foreach ($valuesRaw as $param) {
            $name = $param['name'];
            $values[$name] = $param['value'];
        }
        return $values;
    }
}
