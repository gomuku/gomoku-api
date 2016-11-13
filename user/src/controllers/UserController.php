<?php

namespace Api\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

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
     * Get token action
     * 
     * @param \Slim\Http\Request $req
     * @param \Slim\Http\Response $res
     * @param array $args
     * @return \Slim\Http\Response
     */
    public function getToken(Request $req, Response $res, array $args)
    {
        // validate here
        $body = (object)$req->getParsedBody();

        // get user
        $tbUser = $this->get('db')->table('users');
        $rUser = $tbUser->where('username','=', $body->username)
            ->where('password', '=', md5($body->password))
            ->first();
        
        // get token
        if(!empty($rUser)){
            $tbToken = $this->get('db')->table('tokens');
            $rToken = $tbToken->where('user_id','=', $rUser->id)
                ->where('appid', '=', null)
                ->where('expired', '>=', time())
                ->first();

            // create new tokens after checking ussername and password
            if(empty($rToken)){
                $setting = (object)$this->get('settings')['token'];
                $rToken = [
                    'user_id' => $rUser->id,
                    'token' => $this->_genToken($rUser->id),
                    'expired' => strtotime($setting->timeout)
                ];

                // tracking status save on failed
                if($tbToken->insert($rToken)){
                    $rToken = (object)$rToken;
                }
            }
        } else {
            return $res->withStatus(401)->withJson([
                'error' => [
                    'code' => 401,
                    'message' => 'Wrong username or password. access denied.'
                ]
            ]);
        }

        // reponse result
        return $res->withJson([
            'id' => $rUser->id,
            'username' => $rUser->username,
            'token' => $rToken->token,
            'expired' => $rToken->expired
        ]);
    }
}
