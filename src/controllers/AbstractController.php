<?php

namespace Api\Controller;

use Interop\Container\ContainerInterface;

/**
 * Test View point controller
 */
abstract class AbstractController
{

    /**
     * Slim App
     * @var \Slim\App;
     */
    protected $ci;

    /**
     * Construct
     * 
     * @param \Interop\Container\ContainerInterface $ci
     */
    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
        
        $this->init();
    }

    /**
     * [init description]
     * 
     * @return [type] [description]
     */
    public function init()
    {
        
    }

    /**
     * [get description]
     * 
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function get($name)
    {
        return $this->ci->get($name);
    }

    /**
     * Get table by name
     * 
     * @param string $table table need to get
     * @return \Illuminate\Database\Query\Builder
     */
    public function table($table)
    {
        return $this->get('db')->table($table);
    }

}
