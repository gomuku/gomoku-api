<?php
return [
    'settings' => [
        'displayErrorDetails'    => false, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        // Monolog settings
        'logger'  => [
            'name'  => 'slim-app',
            'path'  => LOGS . '/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
        'token'   => [
            'timeout' => '+2 days',
            'salt'    => '52c664248b560d2874cbefd8a83cf55da515765e6450ea3b2b6a4d878d5cd6a8'
        ],
        // Database connection settings           
        "db"      => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'gomoku_api',
            'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix'    => ''
        ],
        "db_test" => [
            'driver'   => 'sqlite',
            'database' => STORAGE . '/database/testing.sqlite',
            'prefix'   => '',
        ]
    ],
];
