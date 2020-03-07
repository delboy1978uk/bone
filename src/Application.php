<?php declare(strict_types=1);

namespace Bone;

use Barnacle\Container;
use Bone\Http\Middleware\Stack;
use Bone\Router\Router;
use Bone\Router\NotFoundException;
use Bone\Server\Environment;
use Bone\Server\SiteConfig;
use Del\SessionManager;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Psr\Http\Server\RequestHandlerInterface;

class Application
{
    /** @var Container $container */
    private $container;

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
            $inst->container = new Container();
            $inst->container[SessionManager::class] = $session;
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
        $router = $this->container[Router::class] = new Router();
        $config = $env->fetchConfig($this->configFolder, $this->environment);
        $config[Environment::class] = $env;
        $config[SiteConfig::class] = new SiteConfig($config, $env);
        $package = new ApplicationPackage($config, $router);
        $package->addToContainer($this->container);

        return $this->container;
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
        $stack = $this->container->get(Stack::class);
        $response = $stack->handle($request);

        (new SapiEmitter)->emit($response);

        return true;
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
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