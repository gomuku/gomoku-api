<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/paths.php';

// Instantiate the app
$settings = require CONFIG . '/settings.php';
$app = new \Slim\App($settings);

// load bootstrap
require CONFIG . '/bootstrap.php';

// Run app
$app->run();
