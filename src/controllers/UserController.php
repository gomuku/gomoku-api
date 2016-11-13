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
     * @return \Slim\Http\Response
     */
    public function getToken(Request $req, Response $res)
    {
        // validate here
        $body = (object) $req->getParsedBody();

        // get user
        $user = $this->table('users')
            ->where('username', '=', $body->username)
            ->where('password', '=', md5($body->password))
            ->first();
        if (!$user) {
            return $res->withStatus(403)->withJson([
                'error' => [
                    'code'    => 403,
                    'message' => 'Wrong username or password. access denied.'
                ]
            ]);
        }
        
        // get token
        $tbToken = $this->table('tokens');
        $token  = $tbToken->where('user_id', '=', $user->id)
                ->where('appid', '=', null)
                ->where('expired', '>=', time())
                ->first();

        // create new tokens after checking ussername and password
        if (!$token) {
            $setting  = (object) $this->get('settings')['token'];
            $newToken = [
                'user_id' => $user->id,
                'token'   => $this->_genToken($user->id),
                'expired' => strtotime($setting->timeout)
            ];

            // tracking status save on failed
            if ($tbToken->insert($newToken)) {
                $token = (object) $newToken;
            }
        }

        // reponse result
        return $res->withJson([
            'id'       => $user->id,
            'username' => $user->username,
            'token'    => $token->token,
            'expired'  => $token->expired
        ]);
    }

}
