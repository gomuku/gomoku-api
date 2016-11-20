<?php
// Application middleware
$app->add(new \Slim\Middleware\JwtAuthentication([
    "path"        => "/",
    "passthrough" => "/token",
    "secret"      => $container['settings']['token']['secret'],
     "callback" => function ($req, $res, $args) use ($container) {
         $container["jwt"] = $args["decoded"];
    }
]));
