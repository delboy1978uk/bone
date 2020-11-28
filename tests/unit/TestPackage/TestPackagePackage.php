<?php

declare(strict_types=1);

namespace BoneTest\TestPackage;

use Barnacle\Container;
use Barnacle\EntityRegistrationInterface;
use Barnacle\RegistrationInterface;
use Bone\Console\CommandRegistrationInterface;
use Bone\Http\GlobalMiddlewareRegistrationInterface;
use Bone\Http\Middleware\Stack;
use Bone\I18n\I18nRegistrationInterface;
use Bone\Router\Router;
use Bone\Router\RouterConfigInterface;
use Bone\View\Extension\Plates\AlertBox;
use Bone\View\ViewEngine;
use Bone\View\ViewRegistrationInterface;
use BoneTest\TestPackage\Command\TestCommand;
use BoneTest\TestPackage\Controller\TestPackageApiController;
use BoneTest\TestPackage\Controller\TestPackageController;
use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use League\Route\RouteGroup;
use League\Route\Strategy\JsonStrategy;
use Laminas\Diactoros\ResponseFactory;

class TestPackagePackage implements RegistrationInterface, RouterConfigInterface, I18nRegistrationInterface, GlobalMiddlewareRegistrationInterface, CommandRegistrationInterface, EntityRegistrationInterface, ViewRegistrationInterface
{
    /**
     * @param Container $c
     */
    public function addToContainer(Container $c)
    {
        $c[TestPackageController::class] = $c->factory(function (Container $c) {
            /** @var ViewEngine $viewEngine */
            $viewEngine = $c->get(ViewEngine::class);

            return new TestPackageController($viewEngine);
        });

        $c[TestPackageApiController::class] = $c->factory(function (Container $c) {
            return new TestPackageApiController();
        });
    }

    /**
     * @return array
     */
    public function addViews(): array
    {
        return [
            'testpackage' => __DIR__ . '/View/test/',
            'error' => __DIR__ . '/View/error/',
            'layouts' => __DIR__ . '/View/layouts/',
        ];
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

    /**
     * @param Container $container
     * @return array
     */
    public function getMiddleware(Container $container): array
    {
        return [];
    }

    /**
     * @param Container $c
     * @return array
     */
    public function getGlobalMiddleware(Container $c): array
    {
        return [];
    }

    /**
     * @param Container $c
     * @return array
     */
    public function addViewExtensions(Container $c): array
    {
        $x = new class implements ExtensionInterface {

            public function register(Engine $engine)
            {
                $engine->registerFunction('test', [$this, 'test']);
            }

            public function test(): string
            {
                return 'Whatever, it\' a test.';
            }
        };

        return [$x];
    }


    /**
     * @param Container $container
     * @return array
     */
    public function registerConsoleCommands(Container $container): array
    {
        return [new TestCommand()];
    }
}
