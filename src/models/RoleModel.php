<?php

namespace Api\Model;

use Api\Lib\SingletonTrait;

class RoleModel extends AbstractModel
{

    use SingletonTrait;

    /**
     * connect to table
     * 
     * @var string
     */
    protected $name = 'roles';

}
