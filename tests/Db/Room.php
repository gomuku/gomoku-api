<?php

namespace tests\Db;

use Api\Lib\SingletonTrait;
use Illuminate\Database\Schema\Blueprint as Table;

class Room extends AbstractTable
{
    use SingletonTrait;

    /**
     * table = users.
     */
    protected $name = 'rooms';

    /**
     * [create description].
     *
     * @return [type] [description]
     */
    public function create()
    {
        $this->drop();
        $this->schema()->create($this->name, function (Table $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->json('player_ids')->nullable();
            $table->json('viewer_ids')->nullable();
            $table->boolean('enable');
            $table->timestamps();
        });

        return $this;
    }
}
