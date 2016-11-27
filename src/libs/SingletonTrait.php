<?php

namespace Api\Lib;

/**
 * Singleton patter in php
 * */
trait SingletonTrait
{

    protected static $instance = null;

    /**
     * call this method to get instance
     * */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * protected to prevent clonning 
     * */
    protected function __clone()
    {
        
    }
}
