<?php

namespace Api\Model;

use Api\Lib\CapsuleManagerTrait;

abstract class AbstractModel
{
    /**
     * Use CapsuleManager
     */
    use CapsuleManagerTrait;
    
    /**
     * 
     */
    public function __construct()
    {
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
     * 
     * @param type $modelName
     * @param \Api\Model\callable $callback
     * @return type
     */
    public function model($modelName, callable $callback = null)
    {
        // get ModelManager class instance
        $className = "Api\\Model\\" . $modelName . "Model";
        if (class_exists($className)) {
            if ($callback) {
                $model = call_user_func($className . '::getInstance');
                return $callback($model);
            }
            return $model;
        }
        return null;
    }

}
