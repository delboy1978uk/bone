<?php

use Barnacle\Container;
use Bone\Mvc\ApplicationPackage;
use Bone\Mvc\View\PlatesEngine;
use Bone\Server\Environment;
use Bone\Server\I18nHandler;
use Bone\Service\TranslatorFactory;
use Codeception\Coverage\Subscriber\Local;
use Codeception\TestCase\Test;
use League\Route\Router;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;
use Zend\I18n\Translator\Loader\Gettext;

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
        $package = new ApplicationPackage($config, new Router());
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