<?php

namespace Tests\Db;

use Api\Lib\SingletonTrait;
use Illuminate\Database\Schema\Blueprint as Table;

/**
 * Define roles table
 */
class Role extends AbstractTable
{

    use SingletonTrait;

    /**
     * table = users
     */
    protected $name = 'roles';

    /**
     * [create description]
     * @return [type] [description]
     */
    public function create()
    {
        $this->drop();
        $this->schema()->create($this->name, function(Table $table) {
            $table->increments('id');
            $table->string('role_name')->unique();
            $table->string('description')->nullable();
        });
        return $this;
    }

}
