<?php

namespace Api\Controller;

use Api\Model\UserModel;
use Firebase\JWT\JWT;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * User controller.
 */
class UserController extends AbstractController
{
    /**
     * Index action.
     *
     * @param \Slim\Http\Request  $req
     * @param \Slim\Http\Response $res
     * @param array               $args
     *
     * @return \Slim\Http\Response
     */
    public function index(Request $req, Response $res, array $args)
    {
        return $res->withJson('Hello World!');
    }

    /**
     * Gen token action.
     *
     * @param \Slim\Http\Request  $req
     * @param \Slim\Http\Response $res
     *
     * @return \Slim\Http\Response
     */
    public function login(Request $req, Response $res)
    {
        // get request data
        $body = (object) $req->getParsedBody();

        $user = $this->model('User', function (UserModel $model) use ($body) {
            return $model->findUser($body->username, $body->password);
        });

        if (!$user) {
            $dataResponse = [
                'code'    => 401,
                'status'  => 'NG',
                'message' => 'Wrong username or password.',
            ];

            return $res->withStatus(401)->withJson($dataResponse);
        }

        // create token data
        $scopes = $this->model('User', function (UserModel $model) use ($user) {
            return $model->getScopes($user->id);
        });

        // create response data
        $config = (object) $this->get('settings')['token'];
        $time = time(); 
        $payload = [
            'iss'  => 'gomoku.api',
            'iat ' => $time,
            'nbf'  => $time,
            'exp'  => strtotime($config->expired, $time),
            'data' => [
                'username' => $body->username,
                'password' => $body->password,
                'scopes'   => $scopes
            ]
        ];
        $dataResponse = [
            'code'   => 200,
            'status' => 'OK',
            'token'  => JWT::encode($payload, $config->secret, $config->algorithm)
        ];
        return $res->withJson($dataResponse);
    }

    /**
     * Gen token action.
     *
     * @param \Slim\Http\Request  $req
     * @param \Slim\Http\Response $res
     *
     * @return \Slim\Http\Response
     */
    public function token(Request $req, Response $res)
    {
        // check login token
        $login = $this->getLoginInfoFromJwt();
        if(!$login){
            $dataResponse = [
                'code'    => 401,
                'status'  => 'NG',
                'message' => 'Token\'s invalid.',
            ];
            return $res->withStatus(401)->withJson($dataResponse);
        }

        // get request data
        $body = (object) $req->getParsedBody();

        // create token data
        $time = time(); 
        $payload = [
            'iss'  => 'gomoku.api',
            'iat ' => $time,
            'nbf'  => $time,
            'data' => [
                'username' => $login->username,
                'password' => $login->password,
                'scopes'   => array_intersect($body->scopes, $login->scopes)
            ]
        ];

        // create response data
        $config = (object) $this->get('settings')['token'];
        $dataResponse = [
            'code'   => 200,
            'status' => 'OK',
            'token'  => JWT::encode($payload, $config->secret, $config->algorithm)
        ];

        return $res->withJson($dataResponse);
    }
}
