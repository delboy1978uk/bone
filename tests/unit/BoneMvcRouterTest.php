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

    protected $request;
    protected $router;
    protected $registry;

    protected function _before()
    {
        $get = array('getParam1' => 'getParam1Value');
        $post = array('postParam1' => 'postParam1Value');
        $cookie = array('cookieParam1' => 'cookieParam1Value');
        $server = array('REQUEST_URI' => '/',
                        'REQUEST_METHOD' => 'POST');

        $this->request = new Request( $get, $post, $cookie, $server);
        $this->registry = Registry::ahoy();
        $this->registry->set('routes', array(
            '/' => array(
                'controller' => 'index',
                'action' => 'index',
                'params' => array(),
            )
        ));
        $this->router = new Router($this->request);

    }

    protected function _after()
    {
    }

    // tests
    public function testParseReturnsResponse()
    {
        $this->assertInstanceOf('Bone\Mvc\Request',$this->router->dispatch());
    }

    public function test()
    {

    }

}