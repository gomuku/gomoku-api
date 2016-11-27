<?php

namespace Api\Controller;

use Api\Model\UserModel;
use Interop\Container\ContainerInterface;

/**
 * Test View point controller.
 */
abstract class AbstractController
{
    /**
     * Slim App.
     *
     * @var \Slim\App;
     */
    protected $ci;

    /**
     * Construct.
     *
     * @param \Interop\Container\ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
        $this->init();
    }

    /**
     * [init description].
     *
     * @return [type] [description]
     */
    public function init()
    {
    }

    /**
     * [get description].
     *
     * @param [type] $name [description]
     *
     * @return [type] [description]
     */
    public function get($name)
    {
        return $this->ci->get($name);
    }

    /**
     * @param type                     $name
     * @param \Api\Controller\callable $callback
     *
     * @return type
     */
    public function model($name, callable $callback = null)
    {
        // get Model class instance
        $className = "Api\\Model\\{$name}Model";
        $model = call_user_func($className . '::getInstance');
        if ($callback) {
            return $callback($model);
        }
        return $model;
    }
}
