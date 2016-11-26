<?php
// Application middleware
$app->add(new \Slim\Middleware\JwtAuthentication([
    'path'        => ['/'],
    'passthrough' => ['/login'],
    'logger'      => $container['logger'],
    'secret'      => $container['settings']['token']['secret'],
    'callback'    => function ($req, $res, $args) use ($container) {
        $container['jwt'] = $args['decoded'];
    },
    'error' => function ($req, $res, $args) {
        $data = [
            'code'    => 401,
            'status'  => 'NG',
            'message' => $args['message']
        ];
        return $res->withStatus(401)->withJson($data);
    }
]));