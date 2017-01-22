<?php

use Bone\Mvc\Router;
use Bone\Mvc\Registry;
use Zend\Diactoros\ServerRequest as Request;

class BoneMvcRouterTest extends \Codeception\TestCase\Test
{
   /**
    * @var \UnitTester
    */
    protected $tester;

    protected $get;
    protected $post;
    protected $cookie;
    protected $server;
    protected $routes;

    protected $request;
    protected $router;
    protected $registry;

    protected function _before()
    {
        $this->get = array('getParam1' => 'getParam1Value');
        $this->post = array('postParam1' => 'postParam1Value');
        $this->cookie = array('cookieParam1' => 'cookieParam1Value');
        $this->server = array('REQUEST_URI' => '/test',
                              'REQUEST_METHOD' => 'POST');
        $this->routes = array(
            '/' => array(
                'controller' => 'index',
                'action' => 'index',
                'params' => array(),
            ),
            '/test' => array(
                'controller' => 'index',
                'action' => 'test',
                'params' => array(
                    'drink' => 'grog',
                    'speak' => 'pirate',
                ),
            ),
            '/custom/:mandatory/[:optional]' => array(
                'controller' => 'index',
                'action' => 'test',
                'params' => array(
                    'drink' => 'grog',
                    'speak' => 'pirate',
                ),
            ),
        );

        $this->registry = Registry::ahoy();
        $this->registry->set('routes', $this->routes);

        $this->request = new Request();

    }

    protected function _after()
    {
    }


    public function testControllerMatch()
    {
        $this->server['REQUEST_URI'] = '/the-lone-pirate';
        $this->request = new Request();
        $this->router = new Router($this->request);
        $this->router->parseRoute();
        $this->assertEquals('error', $this->router->getController());
    }

    public function testControllerActionMatch()
    {
        $this->server['REQUEST_URI'] = '/treasure/chest';
        $this->request = new Request( );
        $this->router = new Router($this->request);
        $this->router->parseRoute();
        $this->assertEquals('error', $this->router->getController());
    }

    public function testControllerActionParamsMatch()
    {
        $this->server['REQUEST_URI'] = '/treasure/chest/value/100/contents/gold';
        $this->request = new Request();
        $this->router = new Router($this->request);
        $this->router->parseRoute();
        $this->assertEquals('error', $this->router->getController());
    }

    public function testHomePageMatch()
    {
        $this->server['REQUEST_URI'] = '/';
        $this->request = new Request();
        $this->router = new Router($this->request);
        $this->assertEquals('index', $this->router->getController());
        $this->assertEquals('index', $this->router->getAction());
    }

    public function testMandatoryParamsMatch()
    {
        $this->server['REQUEST_URI'] = '/custom/cutlass';
        $this->request = new Request();
        $this->router = new Router($this->request);
        $this->router->parseRoute();
        $this->assertEquals('error', $this->router->getController());
    }

    public function testOptionalParamsMatch()
    {
        $this->server['REQUEST_URI'] = '/custom/eye/patch';
        $this->request = new Request();
        $this->router = new Router($this->request);
        $this->router->parseRoute();
        $this->assertEquals('error', $this->router->getController());
    }

}