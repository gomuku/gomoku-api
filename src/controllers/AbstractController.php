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
     * [getLoginInfo description]
     * @return [type]      [description]
     */
    protected function getLoginInfoFromJwt()
    {
        // get data from token
        $jwt = $this->get('jwt');
        if(!isset($jwt->data)){
            return null;
        }

        // check user exists in db
        $login = $jwt->data;
        $user = $this->model('User', function (UserModel $model) use ($login) {
            return $model->findUser($login->username, $login->password);
        });

        if (!$user) {
            return null;
        }

        // get db scopes
        $dbScopes =  $this->model('User', function (UserModel $model) use ($user) {
            return $model->getScopes($user->id);
        });
        foreach($login->scopes as $scope){
            if(!in_array($scope, $dbScopes)){
                return null;
            }
        }
        $login->userId = $user->id;
        return $login;
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
