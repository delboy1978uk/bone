<?php

namespace Bone;

use Barnacle\Container;
use Barnacle\RegistrationInterface;
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
use Bone\View\ViewEngine;
use Bone\I18n\Service\TranslatorFactory;
use Bone\View\ViewPackage;
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
        $this->initMiddlewareStack($c);
        $this->setupTranslator($c);
        $this->setupPackages($c);
        $this->setupVendorViewOverrides($c);
        $this->setupDownloadController($c);
        $this->setupRouteFirewall($c);
        $this->setupMiddlewareStack($c);
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
    private function setupPackages(Container $c)
    {
        // set up the modules and vendor package modules
        $packages = $c->get('packages');
        $i18n = $c->get('i18n');
        /** @var Translator $translator */
        $translator = $c->get(Translator::class);

        foreach ($packages as $packageName) {
            if (class_exists($packageName)) {
                /** @var RegistrationInterface $package */
                $package = new $packageName();

                if ($package->hasEntityPath()) {
                    $paths = $c['entity_paths'];
                    $paths[] = $package->getEntityPath();
                    $c['entity_paths'] = $paths;
                }
            }
        }
        reset($packages);
        foreach ($packages as $packageName) {
            if (class_exists($packageName)) {
                /** @var RegistrationInterface $package */
                $package = new $packageName();
                $package->addToContainer($c);

                if ($package instanceof RouterConfigInterface) {
                    $package->addRoutes($c, $this->router);
                }

                if ($package instanceof I18nRegistrationInterface) {
                    foreach ($i18n['supported_locales'] as $locale) {
                        $factory = new TranslatorFactory();
                        $factory->addPackageTranslations($translator, $package, $locale);
                    }
                }

                if ($package instanceof MiddlewareAwareInterface) {
                    $stack = $c->get(Stack::class);
                    $package->addMiddleware($stack, $c);
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
     * @param array $registeredViews
     * @param ViewEngine $viewEngine
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
     * @return string
     */
    public function getEntityPath(): string
    {
        return '';
    }

    /**
     * @return bool
     */
    public function hasEntityPath(): bool
    {
        return false;
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
