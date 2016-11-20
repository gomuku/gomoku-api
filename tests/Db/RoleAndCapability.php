<?php

namespace Tests\Db;

use Api\Lib\SingletonTrait;
use Illuminate\Database\Schema\Blueprint as Table;

class RoleAndCapability extends AbstractTable
{

    use SingletonTrait;

    /**
     * table = users
     */
    protected $name = 'roles_capabilities';

    /**
     * [create description]
     * @return [type] [description]
     */
    public function create()
    {
        $this->drop();
        $this->schema()->create($this->name, function(Table $table) {
            $table->increments('id');
            $table->integer('role_id');
            $table->integer('capability_id');
            $table->boolean('allowed');
            $table->timestamps();
        }
        );
        return $this;
    }

}
