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
            '/user/activate/:email/:token' => [
                'controller' => 'index',
                'action' => 'activate',
                'params' => [],
            ],
            '/custom/:mandatory[/:optional]' => array(
                'controller' => 'index',
                'action' => 'test',
                'params' => array(
                    'drink' => 'grog',
                    'speak' => 'pirate',
                ),
            ),
        );

        Registry::ahoy()->set('routes', $this->routes);

        $this->request = new Request();

    }

    protected function _after()
    {
    }


    public function testControllerMatch()
    {
        $this->server['REQUEST_URI'] = '/the-lone-pirate';
        $this->request = new Request(
            $this->server, [], 'http://bone/the-lone-pirate', 'GET'

        );
        $this->router = new Router($this->request);
        $this->router->parseRoute();
        $this->assertEquals('the-lone-pirate', $this->router->getController());
    }

    public function testControllerActionMatch()
    {
        $this->server['REQUEST_URI'] = '/treasure/chest';
        $this->request = new Request(
            $this->server, [], 'http://bone/treasure/chest', 'GET'

        );
        $this->router = new Router($this->request);
        $this->router->parseRoute();
        $this->assertEquals('treasure', $this->router->getController());
        $this->assertEquals('chest', $this->router->getAction());
    }

    public function testControllerActionParamsMatch()
    {
        $this->server['REQUEST_URI'] = '/treasure/chest/value/100/contents/gold';
        $this->request = new Request(
            $this->server, [], 'http://bone/treasure/chest/value/100/contents/gold', 'GET'

        );
        $this->router = new Router($this->request);
        $this->router->parseRoute();
        $this->assertEquals('treasure', $this->router->getController());
        $this->assertEquals('chest', $this->router->getAction());
        $this->assertArrayHasKey('value', $this->router->getParams());
        $this->assertArrayHasKey('contents', $this->router->getParams());
        $this->assertEquals('100', $this->router->getParams()['value']);
        $this->assertEquals('gold', $this->router->getParams()['contents']);
    }

    public function testCustomRouteMatch()
    {
        $this->server['REQUEST_URI'] = '/custom/ship';
        $this->request = new Request(
            $this->server, [], 'http://bone/custom/ship', 'POST'

        );

        $this->router = new Router($this->request);
        $this->router->parseRoute();
        $this->assertEquals('index', $this->router->getController());
        $this->assertEquals('test', $this->router->getAction());
        $this->assertEquals('ship', $this->router->getParams()['mandatory']);
        $this->assertArrayHasKey('drink', $this->router->getParams());
        $this->assertArrayHasKey('speak', $this->router->getParams());
        $this->assertEquals('grog', $this->router->getParams()['drink']);
        $this->assertEquals('pirate', $this->router->getParams()['speak']);

        $this->server['REQUEST_URI'] = '/user/activate/jhgiyfgiy@email.com/c39e46af9cc84290a0724bcb03229ccb';
        $this->request = new Request(
            $this->server, [], 'http://bone/user/activate/jhgiyfgiy@email.com/c39e46af9cc84290a0724bcb03229ccb', 'GET'

        );
        $this->router = new Router($this->request);
        $this->router->parseRoute();
        $this->assertEquals('index', $this->router->getController());
        $this->assertEquals('activate', $this->router->getAction());
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