<?php

return [
    'development config' => 'hello',
    'db' => [
        'driver' => 'pdo_mysql',
        'host' => 'mariadb',
        'database' => 'awesome',
        'dbname' => 'awesome',
        'user' => 'dbuser',
        'pass' => '[123456]',
        'password' => '[123456]',
    ],
    'error_log' => 'tests/_data/log/error_log',
    'error_reporting' => -1,
    'display_errors' => false,
];