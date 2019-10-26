<?php
// This is global bootstrap for autoloading

require_once __DIR__.'/../vendor/autoload.php'; // composer autoload

$_SERVER = [
    'SERVER_NAME' => 'sinking.ship',
    'REMOTE_ADDR' => '192.168.6.6',
    'HTTP_USER_AGENT' => 'Some brand new smart phone',
];

