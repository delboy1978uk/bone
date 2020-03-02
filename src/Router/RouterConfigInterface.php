<?php

namespace Bone\Mvc\Router;

use Barnacle\Container;
use Bone\Router;

interface RouterConfigInterface
{
    public function addRoutes(Container $c, Router $router);
}