<?php

namespace Tests\Db;

use Illuminate\Database\Schema\Blueprint as Table;

class User  extends AbstractTable
{

    /**
     * table = users
     */
    protected $table = 'users';

    /**
     * [create description]
     * @return [type] [description]
     */
    public function create()
    {
        return $this->schema->create(
            $this->table, 
            function(Table $table) {
                $table->increments('id');
                $table->string('username')->unique();
                $table->string('password');
                $table->string('email');
                $table->string('fullname');
            }
        );
    }
}
