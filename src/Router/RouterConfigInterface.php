<?php

namespace Bone\Router\Router;

use Barnacle\Container;
use Bone\Router\Router;

interface RouterConfigInterface
{
    public function addRoutes(Container $c, Router $router);
}