<?php

namespace Api\Model;

use Illuminate\Database\Capsule\Manager as Capsule;

abstract class AbstractManager
{

    /**
     *
     * @var type 
     */
    protected static $instance;

    /**
     *
     * @var type 
     */
    protected $name;

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
     *
     * @var type 
     */
    protected $table;

    /**
     * 
     */
    public function __construct()
    {
        // set up connection
        $this->connection = Capsule::connection();
        $this->schema     = Capsule::schema();
        $this->table      = Capsule::table($this->name);

        // init for inheritic
        $this->init();
    }

    /**
     * inherit
     */
    public function init()
    {
        
    }

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * Magic methof
     * 
     * @param string $name
     * @param string $arguments
     * @return mixed
     */
    public function __call($name, $arguments = [])
    {
        // get ModelManager class instance
        $className = "Api\\Model\\" . str_replace("get", "", $name);
        if (class_exists($className)) {
            return call_user_func($className . '::getInstance');
        }

        return null;
    }

}
