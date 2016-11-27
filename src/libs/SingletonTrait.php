<?php

namespace Api\Lib;

/**
 * Singleton patter in php.
 * */
trait SingletonTrait
{
    protected static $instance = null;

    /**
     * Call this method to get instance.
     *
     * @return mixed
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }

        return static::$instance;
    }
}
