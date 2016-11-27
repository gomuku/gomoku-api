<?php

namespace Api\Model;

use Api\Lib\SingletonTrait;

class CapabilityModel extends AbstractModel
{

    use SingletonTrait;

    /**
     * connect to table
     * 
     * @var string
     */
    protected $name = 'capabilities';

    /**
     * 
     * @param type $id
     * @return type
     */
    public function getCapabilityName($id)
    {
        if ($cap = $this->table()->find($id)) {
            return $cap->capability_name;
        }
        return null;
    }

}
