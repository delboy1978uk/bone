<?php

namespace Bone\Mvc;

use Barnacle\Container;
use Barnacle\RegistrationInterface;
use Bone\Mvc\Router\PlatesStrategy;
use Bone\Mvc\Router\RouterConfigInterface;
use Bone\Server\Environment;
use BoneMvc\Module\Dragon\Controller\DragonController;
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
     * @param array $config
     */
    private function configureContainer(array $config, $router)
    {
        $c = $this->treasureChest;

        // add the config array
        foreach($config as $key => $value)
        {
            $c[$key] = $value;
        }

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
                    $package->addRoutes($c, $router);
                }
            }
        }

        foreach ($packages as $module) {
            $packageName  = '\BoneMvc\Module\\'.$module.'\\'.$module . 'Package';
            if (class_exists($packageName)) {
                /** @var RegistrationInterface $package */
                $package = new PackageName();
                if ($package->hasEntityPath()) {
                    $c['entity_paths'][] = $package->getEntityPath();
                }
                $package->addToContainer($c);

                if ($package instanceof RouterConfigInterface) {
                    $package->addRoutes($c, $router);
                }
            }
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
        // load in the config and set up the dependency injection container
        $env = new Environment($_SERVER);
        $request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        $router = new Router();
        $strategy = (new PlatesStrategy())->setContainer($this->treasureChest);

        $router->setStrategy($strategy);
        $config = $env->fetchConfig($this->configFolder, $this->environment);
        $this->configureContainer($config, $router);

        $response = $router->dispatch($request);

        // send the response
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