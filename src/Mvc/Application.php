<?php

namespace Bone\Mvc;

use Bone\Server\Environment;
use League\Route\Router;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequestFactory;
use Zend\Diactoros\Response;
use Zend\HttpHandlerRunner\Emitter\SapiEmitter;

class Application
{
    /** @var Registry $registry */
    private $registry;

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
    public static function ahoy(array $config = [])
    {
        static $inst = null;
        if ($inst === null)
        {
            $inst = new Application();
            $inst->registry = Registry::ahoy();
            $inst->treasureChest = new Container();
            $inst->setConfig($config);
            $env = getenv('APPLICATION_ENV');
            if ($env) {
                $inst->setEnvironment($env);
            }
        }
        return $inst;
    }

    /**
     * @param array $config
     */
    private function setConfig(array $config)
    {
        foreach($config as $key => $value)
        {
            $this->registry->set($key,$value);
            $this->treasureChest[$key] = $value;
        }
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
        $env = new Environment($_SERVER);
        if (!count($this->registry->getAll())) {
            $config = $env->fetchConfig($this->configFolder, $this->environment);
            $this->setConfig($config);
        }
        $request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        $response = new Response();

        $router = new Router();

        $router->map('GET', '/', function (ServerRequestInterface $request) : ResponseInterface {
            $response = new \Zend\Diactoros\Response;
            $response->getBody()->write('<h1>Hello, World!</h1>');
            return $response;
        });

        $response = $router->dispatch($request);

        // send the response to the browser
        (new SapiEmitter)->emit($response);

//        $dispatcher = new Dispatcher($request, $response, $env);
//        $dispatcher->fireCannons();

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