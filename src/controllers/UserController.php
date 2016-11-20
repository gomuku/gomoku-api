<?php

namespace Api\Controller;

use Firebase\JWT\JWT;
use Slim\Http\Request;
use Slim\Http\Response;
use Api\Model\UserModel;

/**
 * User controller
 */
class UserController extends AbstractController
{
    /**
     * Index action
     * 
     * @param \Slim\Http\Request $req
     * @param \Slim\Http\Response $res
     * @param array $args
     * @return \Slim\Http\Response
     */
    public function index(Request $req, Response $res, array $args)
    {
        return $res->withJson('Hello World!');
    }

    /**
     * Gen token action
     * 
     * @param \Slim\Http\Request $req
     * @param \Slim\Http\Response $res
     * @return \Slim\Http\Response
     */
    public function genToken(Request $req, Response $res)
    {
        // get request data
        $body = (object) $req->getParsedBody();
        $user = $this->model('User', 
            function(UserModel $model) use($body) {
                return $model->findUser($body->username, $body->password);
            }
        );

        if (!$user) {
            $dataResponse = [
                'code'    => 401,
                'status'  => 'NG',
                'message' => 'Wrong username or password.'
            ];
            return $res->withStatus(401)->withJson($dataResponse);
        }


        // create token data
        $scopes = $this->model('User', 
            function(UserModel $model) use($body, $user) {
                return $model->getScopes($user->id, $body->scopes);
            }
        );
        
        $payload = [
            'username' => $body->username,
            'password' => $body->password,
            'scopes'   => $scopes
        ];

        // create response data
        $config       = (object) $this->get('settings')['token'];
        $dataResponse = [
            'code'   => 200,
            'status' => 'OK',
            'token'  => JWT::encode($payload, $config->secret, $config->algorithm)
        ];
        return $res->withJson($dataResponse);
    }

}
