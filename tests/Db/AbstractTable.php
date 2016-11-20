<?php

namespace Tests\Db;

use Api\Lib\CapsuleManagerTrait;

abstract class AbstractTable
{

    /**
     * Use CapsuleManager
     */
    use CapsuleManagerTrait;
    /**
     * [insert description]
     * 
     * @param  array  $data [description]
     * @return [type]       [description]
     */
    public function insert($data = [])
    {
        // Perform potentially risky queries in a transaction for easy rollback.
        $this->table()->insert($data);
        return $this;
    }

    /**
     * [drop description]
     * @return [type] [description]
     */
    public function drop()
    {
        $schema = $this->schema();
        if ($schema->hasTable($this->name)) {
            $schema->drop($this->name);
        }
        return $this;
    }

}
