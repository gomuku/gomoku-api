<?php

namespace Api\Lib;

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Singleton patter in php
 * */
trait CapsuleManagerTrait
{

    /**
     *
     * @var type 
     */
    protected $name;

    /**
     * Get connection
     */
    public function connection($connection = null)
    {
        return Capsule::connection($connection);
    }

    /**
     * Get schema
     */
    public function schema()
    {
        return Capsule::schema();
    }

    /**
     * Get table
     * 
     * @return \Illuminate\Database\Query\Builder
     */
    public function table()
    {
        return Capsule::table($this->name);
    }

}
