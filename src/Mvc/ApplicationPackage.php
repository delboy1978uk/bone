<?php

namespace Bone\Mvc;

use Barnacle\Container;
use Barnacle\Exception\NotFoundException;
use Barnacle\RegistrationInterface;
use Bone\Firewall\FirewallPackage;
use Bone\Http\Middleware\Stack;
use Bone\Http\MiddlewareAwareInterface;
use Bone\I18n\I18nPackage;
use Bone\I18n\I18nRegistrationInterface;
use Bone\I18n\View\Extension\LocaleLink;
use Bone\I18n\View\Extension\Translate;
use Bone\Mvc\Controller\DownloadController;
use Bone\Mvc\Router;
use Bone\Mvc\Router\Decorator\ExceptionDecorator;
use Bone\Mvc\Router\Decorator\NotAllowedDecorator;
use Bone\Mvc\Router\Decorator\NotFoundDecorator;
use Bone\Mvc\Router\PlatesStrategy;
use Bone\Mvc\Router\RouterConfigInterface;
use Bone\Mvc\View\Extension\Plates\AlertBox;
use Bone\Mvc\View\ViewEngine;
use Bone\View\Helper\Paginator;
use Bone\Mvc\View\PlatesEngine;
use Bone\I18n\Service\TranslatorFactory;
use League\Plates\Template\Folders;
use League\Route\Strategy\ApplicationStrategy;
use League\Route\Strategy\JsonStrategy;
use Locale;
use PDO;
use Laminas\Diactoros\ResponseFactory;
use Laminas\I18n\Translator\Loader\Gettext;
use Laminas\I18n\Translator\Translator;
use Psr\Http\Server\MiddlewareInterface;

class ApplicationPackage implements RegistrationInterface
{
    /** @var array $config */
    private $config;

    /** @var Router $router */
    private $router;

    /** @var bool $i18nEnabledSite */
    private $i18nEnabledSite = false;

    /** @var array $supportedLocales */
    private $supportedLocales = [];

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
     */
    public function addToContainer(Container $c)
    {
        $this->setConfigArray($c);
        $this->setLocale($c);
        $this->setupLogs($c);
        $this->setupPdoConnection($c);
        $this->setupViewEngine($c);
        $this->initMiddlewareStack($c);
        $this->setupTranslator($c);
        $this->setupModules($c);
        $this->setupModuleViewOverrides($c);
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
    private function setLocale(Container $c)
    {
        $i18n = $c->get('i18n');
        $this->i18nEnabledSite = $i18n['enabled'];
        $this->supportedLocales = $i18n['supported_locales'];
        $defaultLocale = $i18n['default_locale'];
        Locale::setDefault($defaultLocale);
    }

    /**
     * @param Container $c
     */
    private function setupViewEngine(Container $c)
    {
        // set up the view engine dependencies
        $viewEngine = new PlatesEngine($c->get('viewFolder'));
        $viewEngine->loadExtension(new AlertBox());

        $c[PlatesEngine::class] = $viewEngine;

        $c[NotFoundDecorator::class] = $c->factory(function (Container $c) {
            $layout = $c->get('default_layout');
            $templates = $c->get('error_pages');
            $viewEngine = $c->get(PlatesEngine::class);
            $notFoundDecorator = new NotFoundDecorator($viewEngine, $templates);
            $notFoundDecorator->setLayout($layout);

            return $notFoundDecorator;
        });

        $c[NotAllowedDecorator::class] = $c->factory(function (Container $c) {
            $layout = $c->get('default_layout');
            $templates = $c->get('error_pages');
            $viewEngine = $c->get(PlatesEngine::class);
            $notAllowedDecorator = new NotAllowedDecorator($viewEngine, $templates);
            $notAllowedDecorator->setLayout($layout);

            return $notAllowedDecorator;
        });

        $c[ExceptionDecorator::class] = $c->factory(function (Container $c) {
            $viewEngine = $c->get(PlatesEngine::class);
            $layout = $c->get('default_layout');
            $templates = $c->get('error_pages');
            $decorator = new ExceptionDecorator($viewEngine, $templates);
            $decorator->setLayout($layout);

            return $decorator;
        });

        $c[PlatesStrategy::class] = $c->factory(function (Container $c) {
            $viewEngine = $c->get(PlatesEngine::class);
            $notFoundDecorator = $c->get(NotFoundDecorator::class);
            $notAllowedDecorator = $c->get(NotAllowedDecorator::class);
            $exceptionDecorator = $c->get(ExceptionDecorator::class);
            $layout = $c->get('default_layout');
            $strategy = new PlatesStrategy($viewEngine, $notFoundDecorator, $notAllowedDecorator, $layout, $exceptionDecorator);

            return $strategy;
        });

        /** @var PlatesStrategy $strategy */
        $strategy = $c->get(PlatesStrategy::class);
        $strategy->setContainer($c);

        if ($this->i18nEnabledSite === true) {
            $strategy->setI18nEnabled(true);
            $strategy->setSupportedLocales($this->supportedLocales);
        }

        $this->router->setStrategy($strategy);
    }

    /**
     * @param Container $c
     */
    private function setupModules(Container $c)
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
                    $package->addMiddleware($stack);
                }
            }
        }
    }

    /**
     * @param Container $c
     */
    private function setupTranslator(Container $c)
    {
        $package = new I18nPackage();
        $package->addToContainer($c);
        $package->addMiddleware($c->get(Stack::class), $c);
    }


    /**
     * @param Container $c
     */
    private function setupPdoConnection(Container $c)
    {
        // set up a db connection
        $c[PDO::class] = $c->factory(function (Container $c): PDO {
            $credentials = $c->get('db');
            $host = $credentials['host'];
            $db = $credentials['database'];
            $user = $credentials['user'];
            $pass = $credentials['pass'];

            $dbConnection = new PDO('mysql:host=' . $host . ';dbname=' . $db, $user, $pass, [
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            return $dbConnection;
        });
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
        $firewallPackage = new FirewallPackage();
        $firewallPackage->addToContainer($c);
    }

    /**
     * @param Container $c
     */
    private function setupLogs(Container $c)
    {
        if ($c->has('display_errors')) {
            ini_set('display_errors', $c->get('display_errors'));
        }

        if ($c->has('error_reporting')) {
            error_reporting($c->get('error_reporting'));
        }

        if ($c->has('error_log')) {
            $errorLog = $c->get('error_log');
            if (!file_exists($errorLog)) {
                file_put_contents($errorLog, '');
                chmod($errorLog, 0775);
            }
            ini_set($c->get('error_log'), $errorLog);
        }
    }

    /**
     * @param Container $c
     */
    private function setupModuleViewOverrides(Container $c): void
    {
        /** @var PlatesEngine $viewEngine */
        $viewEngine = $c->get(PlatesEngine::class);
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
     * @param PlatesEngine $viewEngine
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