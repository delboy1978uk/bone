<?php

namespace Bone;

use Barnacle\Container;
use Barnacle\RegistrationInterface;
use Bone\Http\Middleware\Stack;
use Bone\Mvc\Router;
use Bone\Router\Decorator\ExceptionDecorator;
use Bone\Router\Decorator\NotFoundDecorator;
use Bone\Router\NotFoundException;
use Bone\Router\PlatesStrategy;
use Bone\Router\RouterConfigInterface;
use Bone\View\PlatesEngine;
use Bone\Server\Environment;
use Bone\I18n\Http\Middleware\I18nHandler;
use Bone\Server\SiteConfig;
use Del\SessionManager;
use League\Route\RouteGroup;
use League\Route\Strategy\ApplicationStrategy;
use League\Route\Strategy\JsonStrategy;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\Uri;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\I18n\Translator\Translator;
use Psr\Http\Server\RequestHandlerInterface;

class Application
{
    /** @var Container $registry */
    private $treasureChest;

    /** @var string $configFolder */
    private $configFolder = 'config';

    /** @var string $environment */
    private $environment = 'production';

    /**
     *  There be nay feckin wi' constructors on board this ship
     *  There be nay copyin' o' th'ship either
     *  This ship is a singleton!
     */
    private function __construct(){}

    private function __clone(){}


    /**
     *  Ahoy! There nay be boardin without yer configuration
     *
     * @param array $config
     * @return Application
     */
    public static function ahoy()
    {
        static $inst = null;
        if ($inst === null) {
            $inst = new Application();
            $session = SessionManager::getInstance();
            SessionManager::sessionStart('app');
            $inst->treasureChest = new Container();
            $inst->treasureChest[SessionManager::class] = $session;
            $env = getenv('APPLICATION_ENV');
            if ($env) {
                $inst->setEnvironment($env);
            }
        }
        return $inst;
    }

    /**
     *  Use this to bootstrap Bone without dispatching any request
     *  i.e. for when using the framework in a CLI application
     */
    public function bootstrap(): Container
    {
        $env = new Environment($_SERVER);
        $router = $this->treasureChest[Router::class] = new Router();

        $config = $env->fetchConfig($this->configFolder, $this->environment);
        $config[Environment::class] = $env;
        $config[SiteConfig::class] = new SiteConfig($config, $env);

        $package = new ApplicationPackage($config, $router);
        $package->addToContainer($this->treasureChest);

        return $this->treasureChest;
    }


    /**
     *
     * T' the high seas! Garrr!
     *
     * @return bool
     * @throws \Exception
     */
    public function setSail()
    {
        // load in the config and set up the dependency injection container
        $this->bootstrap();
        $request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        /** @var RequestHandlerInterface $stack */
        $stack = $this->treasureChest->get(Stack::class);

        try {
            $response = $stack->handle($request);
        } catch (NotFoundException $e) {
            $response = new RedirectResponse($e->getMessage());
            if ($e->getRequest()->getMethod() !== 'GET') {
                $response = $stack->handle($request);
            }
        }

        (new SapiEmitter)->emit($response);

        return true;
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->treasureChest;
    }

    /**
     * @param string $configFolder
     */
    public function setConfigFolder(string $configFolder)
    {
        $this->configFolder = $configFolder;
    }

    /**
     * @param string $environment
     */
    public function setEnvironment(string $environment)
    {
        $this->environment = $environment;
    }
}