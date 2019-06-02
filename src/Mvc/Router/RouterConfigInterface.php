<?php

namespace Bone\Mvc\Router;

use Barnacle\Container;
use League\Route\Router;

interface RouterConfigInterface
{
    public function addRoutes(Container $c, Router $router);
}