<?php

declare(strict_types=1);

namespace BoneTest\TestPackage;

use Barnacle\Container;
use Barnacle\RegistrationInterface;
use BoneTest\TestPackage\Controller\TestPackageApiController;
use BoneTest\TestPackage\Controller\TestPackageController;
use Bone\Mvc\Router\RouterConfigInterface;
use Bone\Mvc\View\PlatesEngine;
use League\Route\RouteGroup;
use League\Route\Router;
use League\Route\Strategy\JsonStrategy;
use Zend\Diactoros\ResponseFactory;

class TestPackagePackage implements RegistrationInterface, RouterConfigInterface
{
    /**
     * @param Container $c
     */
    public function addToContainer(Container $c)
    {
        /** @var PlatesEngine $viewEngine */
        $viewEngine = $c->get(PlatesEngine::class);
        $viewEngine->addFolder('testpackage', __DIR__ . '/View/TestPackage/');

        $c[TestPackageController::class] = $c->factory(function (Container $c) {
            /** @var PlatesEngine $viewEngine */
            $viewEngine = $c->get(PlatesEngine::class);

            return new TestPackageController($viewEngine);
        });

        $c[TestPackageApiController::class] = $c->factory(function (Container $c) {
            return new TestPackageApiController();
        });
    }

    /**
     * @return string
     */
    public function getEntityPath(): string
    {
        return 'TestPackage/Entity';
    }

    /**
     * @return bool
     */
    public function hasEntityPath(): bool
    {
        return true;
    }

    /**
     * @param Container $c
     * @param Router $router
     * @return Router
     */
    public function addRoutes(Container $c, Router $router): Router
    {
        $router->map('GET', '/testpackage', [TestPackageController::class, 'indexAction']);
        $router->map('GET', '/another', [TestPackageController::class, 'anotherAction']);
        $router->map('GET', '/bad', [TestPackageController::class, 'badAction']);

        $factory = new ResponseFactory();
        $strategy = new JsonStrategy($factory);
        $strategy->setContainer($c);

        $router->group('/api', function (RouteGroup $route) {
            $route->map('GET', '/testpackage', [TestPackageApiController::class, 'indexAction']);
        })
        ->setStrategy($strategy);

        return $router;
    }
}
