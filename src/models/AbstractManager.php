<?php

namespace Api\Model;

use Illuminate\Database\Capsule\Manager as Capsule;

class AbstractManager
{

    protected $table;
    protected $schema;
    protected $connection;
    private $_table;

    public function __construct()
    {
        // set up connection
        $this->connection = Capsule::connection();
        $this->schema     = Capsule::schema();
        $this->_table     = $this->schema->table($this->table);

        // init for inheritic
        $this->init();
    }

    public function init()
    {
        
    }

}
