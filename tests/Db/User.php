<?php

namespace Tests\Db;

use Api\Lib\SingletonTrait;
use Illuminate\Database\Schema\Blueprint as Table;

class User extends AbstractTable
{

    use SingletonTrait;

    /**
     * table = users
     */
    protected $name = 'users';

    /**
     * [create description]
     * @return [type] [description]
     */
    public function create()
    {
        $this->drop();
        $this->schema()->create($this->name, function(Table $table) {
            $table->increments('id');
            $table->string('username')->unique();
            $table->string('password');
            $table->string('email');
            $table->string('fullname')->nullable();
            $table->integer('role_id')->nullable();
        });
        return $this;
    }

}
