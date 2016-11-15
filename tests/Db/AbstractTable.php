<?php

namespace Tests\Db;

use Illuminate\Database\Capsule\Manager as Capsule;

abstract class AbstractTable
{

    /**
     *
     * @var type 
     */
    protected $schema;

    /**
     *
     * @var type 
     */
    protected $connection;

    /**
     * table = users
     */
    protected $table;

    /**
     * constructor
     */
    public function __construct($connection = null)
    {
        $this->schema     = Capsule::schema();
        $this->connection = Capsule::connection($connection);
    }

    /**
     * [insert description]
     * 
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public function insert($data = [])
    {
        // Perform potentially risky queries in a transaction for easy rollback.
        try {
            $table = $this->table;
            $this->connection->transaction(function ($con) use ($table, $data) {
                $tb = $con->table($table);
                foreach ($data as $row) {
                    $tb->insert($row);
                }
            });
        } catch (\Exception $e) {
            echo "Uh oh! Inserting didn't work, but I was able to rollback. {$e->getMessage()}";
        }
    }

    /**
     * [drop description]
     * @return [type] [description]
     */
    public function drop()
    {
        return $this->schema->drop($this->table);
    }

}
