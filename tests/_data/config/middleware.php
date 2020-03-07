<?php

use Bone\Firewall\RouteFirewall;
use BoneTest\TestPackage\Http\Middleware\TestMiddleware;

return [
    'stack' => [
        RouteFirewall::class,
        'BoneTest\TestPackage\Http\Middleware\TestMiddleware',
        new TestMiddleware(),
    ],
];