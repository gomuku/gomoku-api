<?php
// Application middleware
$app->add(new \Slim\Middleware\JwtAuthentication([
    'path' => ['/'],
    'passthrough' => ['/login'],
    'logger' => $container['logger'],
    'secret' => $container['settings']['token']['secret'],
    'callback' => function ($req, $res, $args) use ($container) {
        $jwt = $args['decoded'];
        $container['jwt'] = $jwt;

        // get data from token
        $data = [
            'code' => 401,
            'status' => 'NG',
            'message' => 'Token\'s invalid.',
        ];
        if (!isset($jwt->data)) {
            return $res->withStatus(401)->withJson($data);
        }

        // check user exists in db
        $login = $jwt->data;
        $userModel = Api\Model\UserModel::getInstance();
        $user = $userModel->findUser($login->username, $login->password);
        if (!$user) {
            return $res->withStatus(401)->withJson($data);
        }

        // get db scopes
        $dbScopes = $userModel->getScopes($user->id);
        foreach ($login->scopes as $scope) {
            if (!in_array($scope, $dbScopes)) {
                return $res->withStatus(401)->withJson($data);
            }
        }
        $login->id = $user->id;
        $container['loginInfo'] = $login;
    },
    'error' => function ($req, $res, $args) {
        $data = [
            'code' => 401,
            'status' => 'NG',
            'message' => $args['message'],
        ];

        return $res->withStatus(401)->withJson($data);
    },
]));
