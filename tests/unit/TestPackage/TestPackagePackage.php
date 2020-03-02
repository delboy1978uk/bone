<?php

declare(strict_types=1);

namespace BoneTest\TestPackage;

use Barnacle\Container;
use Barnacle\RegistrationInterface;
use Bone\I18n\I18nRegistrationInterface;
use Bone\Mvc\Router;
use Bone\Router\RouterConfigInterface;
use Bone\View\PlatesEngine;
use BoneTest\TestPackage\Controller\TestPackageApiController;
use BoneTest\TestPackage\Controller\TestPackageController;
use League\Route\RouteGroup;
use League\Route\Strategy\JsonStrategy;
use Laminas\Diactoros\ResponseFactory;

class TestPackagePackage implements RegistrationInterface, RouterConfigInterface, I18nRegistrationInterface
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

    public function getTranslationsDirectory(): string
    {
        return __DIR__ . '/translations';
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
