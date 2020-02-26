<?php

namespace Bone\Http;

use Barnacle\Container;
use Bone\Http\Middleware\Stack;

interface MiddlewareAwareInterface
{
    /**
     * @param Stack $stack
     */
    public function addMiddleware(Stack $stack, Container $container): void;
}