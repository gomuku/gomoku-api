<?php
// Application middleware
$app->add(new \Slim\Middleware\JwtAuthentication([
    "path"        => "/",
    "passthrough" => "/token",
    "secret"      => $container['settings']['token']['secret']
]));
