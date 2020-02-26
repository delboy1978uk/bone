<?php

namespace Bone\Http;

use Bone\Http\Middleware\Stack;

interface MiddlewareAwareInterface
{
    /**
     * @param Stack $stack
     */
    public function addMiddleware(Stack $stack): void;
}