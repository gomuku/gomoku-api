<?php

namespace Tests\Db;

use Illuminate\Database\Schema\Blueprint as Table;

class Token extends AbstractTable
{

    /**
     * const table = users
     */
    protected $table = 'tokens';

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
                $table->string('user_id');
                $table->string('appid')->nullable();
                $table->string('token');
                $table->string('expired');
                $table->timestamps();
            }
        );
    }

}
