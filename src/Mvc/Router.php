<?php

namespace Bone\Mvc;

use Bone\Route\Router as LeagueRouter;

class Router extends LeagueRouter
{
    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @param Route $route
     */
    public function removeRoute(Route $routeToRemove): void
    {
        foreach ($this->routes as $index => $route) {
            if ($route === $routeToRemove) {
                unset($this->routes[$index]);
            }
        }
    }
}