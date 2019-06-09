<?php

namespace Bone\Mvc;

use Barnacle\Container;
use Barnacle\RegistrationInterface;
use Bone\Mvc\Router\Decorator\ExceptionDecorator;
use Bone\Mvc\Router\Decorator\NotFoundDecorator;
use Bone\Mvc\Router\PlatesStrategy;
use Bone\Mvc\Router\RouterConfigInterface;
use Bone\Mvc\View\PlatesEngine;

class ApplicationPackage implements RegistrationInterface
{
    /** @var array $config */
    private $config;

    /** @var Router $router */
    private $router;

    /**
     * ApplicationPackage constructor.
     * @param array $config
     * @param \League\Route\Router $router
     */
    public function __construct(array $config, \League\Route\Router $router)
    {
        $this->config = $config;
        $this->router = $router;
    }

    /**
     * @param Container $c
     */
    public function addToContainer(Container $c)
    {
        // add the config array
        foreach($this->config as $key => $value)
        {
            $c[$key] = $value;
        }

        $this->setupPdoConnection($c);
        $this->setupModules($c);
        $this->setupViewEngine($c);
    }

    /**
     * @param Container $c
     */
    private function setupViewEngine(Container $c)
    {
        // set up the view engine dependencies
        $c[PlatesEngine::class] = $c->factory(function () {
            return new PlatesEngine('src/App/View');
        });

        $c[NotFoundDecorator::class] = $c->factory(function (Container $c) {
            $viewEngine = $c->get(PlatesEngine::class);
            $notFoundDecorator = new NotFoundDecorator($viewEngine);

            return $notFoundDecorator;
        });

        $c[ExceptionDecorator::class] = $c->factory(function (Container $c) {
            $viewEngine = $c->get(PlatesEngine::class);
            $notFoundDecorator = new ExceptionDecorator($viewEngine);

            return $notFoundDecorator;
        });

        $c[PlatesStrategy::class] = $c->factory(function (Container $c) {
            $viewEngine = $c->get(PlatesEngine::class);
            $notFoundDecorator = $c->get(NotFoundDecorator::class);
            $exceptionDecorator = $c->get(ExceptionDecorator::class);
            $strategy = new PlatesStrategy($viewEngine, $exceptionDecorator, $notFoundDecorator);

            return $strategy;
        });

        $strategy = $c->get(PlatesStrategy::class)->setContainer($c);
        $this->router->setStrategy($strategy);
    }

    /**
     * @param Container $c
     */
    private function setupModules(Container $c)
    {
        // set up the modules and vendor package modules
        $modules = $c->get('modules');
        $packages = $c->get('packages');

        foreach ($modules as $module) {
            $packageName  = '\BoneMvc\Module\\'.$module.'\\'.$module . 'Package';
            if (class_exists($packageName)) {
                /** @var RegistrationInterface $package */
                $package = new $packageName();

                if ($package->hasEntityPath()) {
                    $c['entity_paths'][] = $package->getEntityPath();
                }

                $package->addToContainer($c);

                if ($package instanceof RouterConfigInterface) {
                    $package->addRoutes($c, $this->router);
                }
            }
        }

        foreach ($packages as $module) {
            $packageName  = '\BoneMvc\Module\\'.$module.'\\'.$module . 'Package';
            if (class_exists($packageName)) {
                /** @var RegistrationInterface $package */
                $package = new $packageName();
                if ($package->hasEntityPath()) {
                    $c['entity_paths'][] = $package->getEntityPath();
                }
                $package->addToContainer($c);

                if ($package instanceof RouterConfigInterface) {
                    $package->addRoutes($c, $this->router);
                }
            }
        }
    }

    /**
     * @param Container $c
     */
    private function setupPdoConnection(Container $c)
    {
        // set up a db connection
        $c[PDO::class] = $c->factory(function(Container $c): PDO {
            $credentials = $c->get('db');
            $host =$credentials['host'];
            $db = $credentials['database'];
            $user = $credentials['user'];
            $pass = $credentials['pass'];

            $dbConnection = new PDO('mysql:host='.$host.';dbname='.$db, $user, $pass, [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            return $dbConnection;
        });
    }

    /**
     * @return string
     */
    function getEntityPath(): string
    {
        return '';
    }

    /**
     * @return bool
     */
    function hasEntityPath(): bool
    {
        return false;
    }
}