<?php

namespace Bone\Mvc;

use Barnacle\Container;
use Barnacle\RegistrationInterface;
use Bone\Mvc\Router\Decorator\ExceptionDecorator;
use Bone\Mvc\Router\Decorator\NotFoundDecorator;
use Bone\Mvc\Router\PlatesStrategy;
use Bone\Mvc\Router\RouterConfigInterface;
use Bone\Mvc\View\PlatesEngine;
use Bone\Server\Environment;
use League\Route\Router;
use League\Route\RouteGroup;
use League\Route\Strategy\ApplicationStrategy;
use League\Route\Strategy\JsonStrategy;
use PDO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

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
    public function __construct(){}
    public function __clone(){}


    /**
     *  Ahoy! There nay be boardin without yer configuration
     *
     * @param array $config
     * @return Application
     */
    public static function ahoy()
    {
        static $inst = null;
        if ($inst === null)
        {
            $inst = new Application();
            $inst->treasureChest = new Container();
            $env = getenv('APPLICATION_ENV');
            if ($env) {
                $inst->setEnvironment($env);
            }
        }
        return $inst;
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
        $env = new Environment($_SERVER);
        $request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        $router = $this->treasureChest[Router::class] = new Router();
        $config = $env->fetchConfig($this->configFolder, $this->environment);
        $package = new ApplicationPackage($config, $router);
        $package->addToContainer($this->treasureChest);
        $response = $router->dispatch($request);

        // send the response
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