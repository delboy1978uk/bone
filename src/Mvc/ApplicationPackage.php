<?php

namespace Bone\Mvc;

use Barnacle\Container;
use Barnacle\RegistrationInterface;
use Bone\Mvc\Router\Decorator\NotAllowedDecorator;
use Bone\Mvc\Router\Decorator\NotFoundDecorator;
use Bone\Mvc\Router\PlatesStrategy;
use Bone\Mvc\Router\RouterConfigInterface;
use Bone\Mvc\View\Extension\Plates\Translate;
use Bone\View\Helper\Paginator;
use Bone\Mvc\View\PlatesEngine;
use Bone\Service\TranslatorFactory;
use League\Route\Router;
use Locale;
use PDO;
use Zend\I18n\Translator\Translator;

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
     * @param \League\Route\Router $router
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
        $this->setupPdoConnection($c);
        $this->setupViewEngine($c);
        $this->setupTranslator($c);
        $this->setupModules($c);
    }

    /**
     * @param Container $c
     */
    private function setConfigArray(Container $c)
    {
        foreach($this->config as $key => $value)
        {
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
//        $i18nExtension = new Translate();
//        $viewEngine->loadExtension();

        $c[PlatesEngine::class] = $viewEngine;

        $c[NotFoundDecorator::class] = $c->factory(function (Container $c) {
            $layout = $c->get('default_layout');
            $viewEngine = $c->get(PlatesEngine::class);
            $notFoundDecorator = new NotFoundDecorator($viewEngine);
            $notFoundDecorator->setLayout($layout);

            return $notFoundDecorator;
        });

        $c[NotAllowedDecorator::class] = $c->factory(function (Container $c) {
            $layout = $c->get('default_layout');
            $viewEngine = $c->get(PlatesEngine::class);
            $notAllowedDecorator = new NotAllowedDecorator($viewEngine);
            $notAllowedDecorator->setLayout($layout);

            return $notAllowedDecorator;
        });

        $c[PlatesStrategy::class] = $c->factory(function (Container $c) {
            $viewEngine = $c->get(PlatesEngine::class);
            $notFoundDecorator = $c->get(NotFoundDecorator::class);
            $notAllowedDecorator = $c->get(NotAllowedDecorator::class);
            $layout = $c->get('default_layout');
            $strategy = new PlatesStrategy($viewEngine, $notFoundDecorator, $notAllowedDecorator, $layout);

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
            }
        }
    }

    /**
     * @param Container $c
     */
    private function setupTranslator(Container $c)
    {
        $config = $c->get('i18n');
        $engine = $c->get(PlatesEngine::class);
        if (is_array($config)) {
            $factory = new TranslatorFactory();
            $translator = $factory->createTranslator($config);
            $c['translator'] = $translator;
            $engine->loadExtension(new Translate($translator));
            $defaultLocale = $config['default_locale'] ?: 'en_GB';
//            if (!in_array($locale, $config['supported_locales'])) {
//                $locale = $defaultLocale;
//            }
            $translator->setLocale($defaultLocale);
            $c[Translator::class] = $translator;
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