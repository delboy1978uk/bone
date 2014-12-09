<?php

use Bone\Mvc\Request;;
use Bone\Mvc\Router;
use Bone\Mvc\Registry;

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

        $this->request = new Request( $this->get, $this->post, $this->cookie, $this->server);

    }

    protected function _after()
    {
    }

    // tests
    public function testParseReturnsResponse()
    {
        $this->router = new Router($this->request);
        $this->assertInstanceOf('Bone\Mvc\Request',$this->router->dispatch());
    }

    public function testControllerMatch()
    {
        $this->server['REQUEST_URI'] = '/the-lone-pirate';
        $this->request = new Request( $this->get, $this->post, $this->cookie, $this->server);
        $this->router = new Router($this->request);
        $request = $this->router->dispatch();
        $this->assertInstanceOf('Bone\Mvc\Request', $request);
    }

    public function testControllerActionMatch()
    {
        $this->server['REQUEST_URI'] = '/treasure/chest';
        $this->request = new Request( $this->get, $this->post, $this->cookie, $this->server);
        $this->router = new Router($this->request);
        $request = $this->router->dispatch();
        $this->assertInstanceOf('Bone\Mvc\Request', $request);
    }

    public function testControllerActionParamsMatch()
    {
        $this->server['REQUEST_URI'] = '/treasure/chest/value/100/contents/gold';
        $this->request = new Request( $this->get, $this->post, $this->cookie, $this->server);
        $this->router = new Router($this->request);
        $request = $this->router->dispatch();
        $this->assertInstanceOf('Bone\Mvc\Request', $request);
    }

    public function testHomePageMatch()
    {
        $this->server['REQUEST_URI'] = '/';
        $this->request = new Request( $this->get, $this->post, $this->cookie, $this->server);
        $this->router = new Router($this->request);
        $request = $this->router->dispatch();
        $this->assertInstanceOf('Bone\Mvc\Request', $request);
    }

    public function testMandatoryParamsMatch()
    {
        $this->server['REQUEST_URI'] = '/custom/cutlass';
        $this->request = new Request( $this->get, $this->post, $this->cookie, $this->server);
        $this->router = new Router($this->request);
        $request = $this->router->dispatch();
        $this->assertInstanceOf('Bone\Mvc\Request', $request);
    }

    public function testOptionalParamsMatch()
    {
        $this->server['REQUEST_URI'] = '/custom/eye/patch';
        $this->request = new Request( $this->get, $this->post, $this->cookie, $this->server);
        $this->router = new Router($this->request);
        $request = $this->router->dispatch();
        $this->assertInstanceOf('Bone\Mvc\Request', $request);
    }

}