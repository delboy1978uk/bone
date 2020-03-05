<?php

use Barnacle\Container;
use Bone\ApplicationPackage;
use Bone\Router\Router;
use Bone\View\ViewEngine;
use Bone\Server\Environment;
use Bone\I18n\Http\Middleware\I18nHandler;
use Bone\I18n\Service\TranslatorFactory;
use Codeception\Coverage\Subscriber\Local;
use Codeception\TestCase\Test;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Laminas\I18n\Translator\Loader\Gettext;

class ApplicationPackageTest extends Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var I18nHandler $middleware */
    private $middleware;

    public function _before()
    {

    }


    public function testPackage()
    {
        $container = new Container();
        $env = new Environment($_SERVER);
        $config = $env->fetchConfig('tests/_data/config', getenv('APPLICATION_ENV'));
        $router = $container[Router::class] = new Router();
        $package = new ApplicationPackage($config, $router);
        $this->assertEquals('', $package->getEntityPath());
        $this->assertFalse($package->hasEntityPath());
        $package->addToContainer($container);
        $pdo = $container->get(PDO::class);
        $this->assertInstanceOf(PDO::class, $pdo);
    }


    /**
     * @param $object
     * @param string $method
     * @throws ReflectionException
     */
    private function runPrivateMethod($object, string $method, ...$args)
    {
        $mirror = new ReflectionClass($object);
        $method = $mirror->getMethod($method);
        $method->setAccessible(true);

        return $method->invoke($object, $args);
    }
}