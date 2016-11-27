<?php

namespace Tests\Db;

use Api\Lib\SingletonTrait;
use Illuminate\Database\Schema\Blueprint as Table;

class Capability extends AbstractTable
{

    use SingletonTrait;

    /**
     * table = users
     */
    protected $name = 'capabilities';

    /**
     * [create description]
     * @return [type] [description]
     */
    public function create()
    {
        $this->drop();
        $this->schema()->create($this->name, function(Table $table) {
            $table->increments('id');
            $table->char('capability_name')->unique();
            $table->string('description')->nullable();
        });
        return $this;
    }

}
