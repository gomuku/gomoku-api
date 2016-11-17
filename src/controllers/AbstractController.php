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

    /**
     * gen string token using key
     * 
     * @param  string $key
     * @return string token string using md5
     */
    protected function _genToken($key = '')
    {
        $setting = (object) $this->get('settings')['token'];
        return md5($key . $setting->salt . time());
    }
    
    /**
     * Check token from d
     * 
     * @param type $appId
     * @param type $token
     */
    protected function _isValidToken($appId, $token)
    {
        
    }

}
