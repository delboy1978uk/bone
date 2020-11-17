<?php

namespace Bone;

use Barnacle\Container;
use Barnacle\EntityRegistrationInterface;
use Barnacle\RegistrationInterface;
use Bone\Console\CommandRegistrationInterface;
use Bone\Console\ConsoleApplication;
use Bone\Console\ConsolePackage;
use Bone\Db\DbPackage;
use Bone\Firewall\FirewallPackage;
use Bone\Http\Middleware\Stack;
use Bone\Http\MiddlewareAwareInterface;
use Bone\I18n\I18nPackage;
use Bone\I18n\I18nRegistrationInterface;
use Bone\Log\LogPackage;
use Bone\Controller\DownloadController;
use Bone\Router\Router;
use Bone\Router\RouterConfigInterface;
use Bone\Router\RouterPackage;
use Bone\View\ViewEngine;
use Bone\I18n\Service\TranslatorFactory;
use Bone\View\ViewPackage;
use Bone\View\ViewRegistrationInterface;
use League\Plates\Template\Folders;
use League\Route\Strategy\JsonStrategy;
use Laminas\Diactoros\ResponseFactory;
use Laminas\I18n\Translator\Translator;
use Psr\Http\Server\MiddlewareInterface;

class ApplicationPackage implements RegistrationInterface
{
    /** @var array $config */
    private $config;

    /** @var Router $router */
    private $router;

    /**
     * ApplicationPackage constructor.
     * @param array $config
     * @param Router $router
     */
    public function __construct(array $config, Router $router)
    {
        $this->config = $config;
        $this->router = $router;
    }

    /**
     * @param Container $c
     * @throws \Bone\Exception
     * @throws \Exception
     */
    public function addToContainer(Container $c)
    {
        $this->setConfigArray($c);
        $this->setupLogs($c);
        $this->setupPdoConnection($c);
        $this->setupViewEngine($c);
        $this->setupRouter($c);
        $this->initMiddlewareStack($c);
        $this->initConsoleApp($c);
        $this->setupTranslator($c);
        $this->setupPackages($c);
        $this->setupVendorViewOverrides($c);
        $this->setupDownloadController($c);
        $this->setupRouteFirewall($c);
        $this->setupMiddlewareStack($c);
        $this->setupConsoleApp($c);
    }

    /**
     * @param Container $c
     */
    private function setConfigArray(Container $c)
    {
        foreach ($this->config as $key => $value) {
            $c[$key] = $value;
        }
    }

    /**
     * @param Container $c
     */
    private function setupViewEngine(Container $c)
    {
        $package = new ViewPackage();
        $package->addToContainer($c);
    }

    /**
     * @param Container $c
     */
    private function setupRouter(Container $c)
    {
        $package = new RouterPackage();
        $package->addToContainer($c);
    }

    /**
     * @param Container $c
     */
    private function setupPackages(Container $c)
    {
        // set up the modules and vendor package modules
        $c['consoleCommands'] = [];
        $packages = $c->get('packages');
        $this->addEntityPathsFromPackages($packages, $c);

        reset($packages);

        foreach ($packages as $packageName) {
            if (class_exists($packageName)) {
                $this->registerPackage($packageName, $c);
            }
        }
    }

    /**
     * @param string $packageName
     * @param Container $c
     */
    private function registerPackage(string $packageName, Container $c): void
    {
        /** @var RegistrationInterface $package */
        $package = new $packageName();
        $package->addToContainer($c);
        $this->registerRoutes($package, $c);
        $this->registerViews($package, $c);
        $this->registerTranslations($package, $c);
        $this->registerMiddleware($package, $c);
        $this->registerConsoleCommands($package, $c);
    }

    /**
     * @param RegistrationInterface $package
     */
    private function registerConsoleCommands(RegistrationInterface $package, Container $c): void
    {
        $consoleCommands = $c->get('consoleCommands');

        if ($package instanceof CommandRegistrationInterface) {
            $commands = $package->registerConsoleCommands($c);

            foreach ($commands as $command) {
                $consoleCommands[] = $command;
            }
        }

        $c['consoleCommands'] = $consoleCommands;
    }

    /**
     * @param RegistrationInterface $package
     */
    private function registerMiddleware(RegistrationInterface $package, Container $c): void
    {
        if ($package instanceof MiddlewareAwareInterface) {
            $stack = $c->get(Stack::class);
            $package->addMiddleware($stack, $c);
        }
    }

    /**
     * @param RegistrationInterface $package
     */
    private function registerRoutes(RegistrationInterface $package, Container $c): void
    {
        if ($package instanceof RouterConfigInterface) {
            $package->addRoutes($c, $this->router);
        }
    }

    /**
     * @param RegistrationInterface $package
     */
    private function registerViews(RegistrationInterface $package, Container $c): void
    {
        if ($package instanceof ViewRegistrationInterface) {
            $views = $package->addViews();
            /** @var ViewEngine $engine */
            $engine = $c->get(ViewEngine::class);

            foreach ($views as $name => $folder) {
                $engine->addFolder($name, $folder);
            }
        }
    }

    /**
     * @param RegistrationInterface $package
     */
    private function registerTranslations(RegistrationInterface $package, Container $c): void 
    {
        $i18n = $c->get('i18n');
        /** @var Translator $translator */
        $translator = $c->get(Translator::class);

        if ($package instanceof I18nRegistrationInterface) {
            foreach ($i18n['supported_locales'] as $locale) {
                $factory = new TranslatorFactory();
                $factory->addPackageTranslations($translator, $package, $locale);
            }
        }
    }

    /**
     * @param Container $c
     */
    private function initConsoleApp(Container $c): void
    {
        $c[ConsoleApplication::class] = new ConsoleApplication();
    }

    /**
     * @param Container $c
     */
    private function setupConsoleApp(Container $c): void
    {
        $package = new ConsolePackage();
        $package->addToContainer($c);
    }

    /**
     * @param array $packages
     * @param Container $c
     */
    private function addEntityPathsFromPackages(array $packages, Container $c): void
    {
        foreach ($packages as $packageName) {
            if (class_exists($packageName)) {
                /** @var RegistrationInterface $package */
                $package = new $packageName();

                if ($package instanceof EntityRegistrationInterface) {
                    $paths = $c['entity_paths'];
                    $paths[] = $package->getEntityPath();
                    $c['entity_paths'] = $paths;
                }
            }
        }
    }

    /**
     * @param Container $c
     * @throws \Bone\Exception
     */
    private function setupTranslator(Container $c)
    {
        $package = new I18nPackage();
        $package->addToContainer($c);
        $package->addMiddleware($c->get(Stack::class), $c);
    }


    /**
     * @param Container $c
     * @throws \Bone\Exception
     */
    private function setupPdoConnection(Container $c)
    {
        $package = new DbPackage();
        $package->addToContainer($c);
    }

    /**
     * @param Container $c
     */
    private function setupDownloadController(Container $c): void
    {
        $uploadDirectory = $c->get('uploads_dir');
        $c[DownloadController::class] = new DownloadController($uploadDirectory);
        $strategy = new JsonStrategy(new ResponseFactory());
        $strategy->setContainer($c);
        $this->router->map('GET', '/download', [DownloadController::class, 'downloadAction'])->setStrategy($strategy);
    }

    /**
     * @param Container $c
     */
    private function setupRouteFirewall(Container $c): void
    {
        $pckage = new FirewallPackage();
        $pckage->addToContainer($c);
    }

    /**
     * @param Container $c
     * @throws \Exception
     */
    private function  setupLogs(Container $c)
    {
        $package = new LogPackage();
        $package->addToContainer($c);
    }

    /**
     * @param Container $c
     */
    private function setupVendorViewOverrides(Container $c): void
    {
        /** @var ViewEngine $viewEngine */
        $viewEngine = $c->get(ViewEngine::class);
        $views = $c->get('views');
        $registeredViews = $viewEngine->getFolders();

        foreach ($views as $view => $folder) {
            $this->overrideViewFolder($view, $folder, $registeredViews);
        }
    }

    /**
     * @param string $view
     * @param string $folder
     * @param Folders $registeredViews
     */
    private function overrideViewFolder(string $view, string $folder, Folders $registeredViews): void
    {
        if ($registeredViews->exists($view)) {
            /** @var \League\Plates\Template\Folder $currentFolder */
            $currentFolder = $registeredViews->get($view);
            $currentFolder->setPath($folder);
        }
    }

    /**
     * @param Container $c
     */
    private function initMiddlewareStack(Container $c): void
    {
        $router = $c->get(Router::class);
        $c[Stack::class] = new Stack($router);
    }

    /**
     * @param Container $c
     */
    private function setupMiddlewareStack(Container $c): void
    {
        $stack = $c->get(Stack::class);
        $middlewareStack = $c->has('stack') ? $c->get('stack') : [];

        foreach ($middlewareStack as $middleware) {
            if ($middleware instanceof MiddlewareInterface) {
                $stack->addMiddleWare($middleware);
            } elseif ($c->has($middleware)) {
                $stack->addMiddleWare($c->get($middleware));
            } else {
                $stack->addMiddleWare(new $middleware());
            }
        }
    }
}
