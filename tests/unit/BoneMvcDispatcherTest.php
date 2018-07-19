<?php

use Bone\Mvc\Dispatcher;
use Bone\Mvc\Controller;
use Bone\Mvc\Registry;
use Bone\Mvc\View\PlatesEngine;
use Psr\Http\Message\ServerRequestInterface ;
use Psr\Http\Message\ResponseInterface;
use AspectMock\Test;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;

class BoneMvcDispatcherTest extends \Codeception\TestCase\Test
{
   /**
    * @var \UnitTester
    */
    protected $tester;

    /** @var ServerRequestInterface  */
    protected $request;

    /** @var ResponseInterface */
    protected $response;

    /** @var Dispatcher */
    protected $dispatcher;

    protected function _before()
    {
        Test::spec('\App\Controller\ErrorController');
        $this->request = new ServerRequest();
        $this->response = new Response();
    }

    protected function _after()
    {
        Test::clean();
    }

    public function testCheckControllerExists()
    {
        // check for an obviouslly existant class
        $dispatcher = new Dispatcher($this->request,$this->response);
        $this->setPrivateProperty($dispatcher,'config',[
            'controller_name' => 'DateTime'
        ]);
        $this->assertTrue($this->invokeMethod($dispatcher,'checkControllerExists'));
    }

    public function testCheckActionExists()
    {
        // check for an obviously existent class
        $dispatcher = new Dispatcher($this->request,$this->response);
        $this->setPrivateProperty($dispatcher,'controller',new DateTime());
        $this->setPrivateProperty($dispatcher,'config',[
            'action_name' => 'modify',
        ]);
        $this->assertTrue($this->invokeMethod($dispatcher,'checkActionExists'));
    }


    public function testSetNotFound()
    {
        $dispatcher = new Dispatcher($this->request,$this->response);
        $this->assertNull($this->invokeMethod($dispatcher,'setNotFound'));
    }


    public function testTemplateCheck()
    {
        $plates = new PlatesEngine(__DIR__.DIRECTORY_SEPARATOR);
        Registry::ahoy()->set('templates','blah');
        $dispatcher = new Dispatcher($this->request,$this->response);
        $controller = new Controller($this->request);
        $this->setPrivateProperty($controller,'viewEngine',$plates);
        $this->assertEquals("<h1>Layout Template</h1>\n<p>moreblah</p>",$this->invokeMethod($dispatcher,'templateCheck',[$controller,'moreblah']));
    }


    public function testPlunderEnemyShip()
    {
        $plates = new PlatesEngine(__DIR__.DIRECTORY_SEPARATOR);
        Registry::ahoy()->set('templates','blah');

        $dispatcher = new Dispatcher($this->request,$this->response);
        $this->setPrivateProperty($dispatcher,'config',[
            'action_name' => 'init',
        ]);
        $controller = new Controller($this->request);
        $this->setPrivateProperty($controller,'viewEngine',$plates);
        $this->setPrivateProperty($dispatcher,'controller',$controller);
        $this->assertNull($this->invokeMethod($dispatcher,'plunderEnemyShip'));
    }


    /**
     *  check it be runnin through setting the destination
     */
    public function testSinkingShip()
    {
        Registry::ahoy()->set('templates', null);
        Test::double(new Bone\Mvc\Controller($this->request),[ 'errorAction' => null, 'notFoundAction' => null]);
        $dispatcher = new Dispatcher($this->request,$this->response);
        $result = $this->invokeMethod($dispatcher,'sinkingShip',[new Bone\Exception('argh')]);
        $this->assertEquals('500 Page Error.', $this->controller->getBody());
    }

    public function testGetTemplateName()
    {
        $dispatcher = new Dispatcher($this->request,$this->response);
        $output = $this->invokeMethod($dispatcher, 'getTemplateName', [null]);
        $this->assertNull($output);
        $output = $this->invokeMethod($dispatcher, 'getTemplateName', ['pirated-template']);
        $this->assertEquals('pirated-template', $output);
        $output = $this->invokeMethod($dispatcher, 'getTemplateName', [['pirated-template']]);
        $this->assertEquals('pirated-template', $output);
    }

//    public function testHandleException()
//    {
//        Registry::ahoy()->set('templates', null);
//        $fakeController = Test::spec(new Bone\Mvc\Controller($this->request),[
//            'errorAction' => null,
//            'notFoundAction' => null,
//            'viewEnabled' => true,
//            'getViewEngine' => new Exception('gaaaargh!'),
//        ]);
//        $dispatcher = new Dispatcher($this->request,$this->response);
//        $this->setPrivateProperty($dispatcher, 'controller', $fakeController);
//        $this->assertEquals('404 Page Not Found.',$dispatcher->fireCannons());
//    }


    /**
     *  check it be runnin through setting the destination
     *
     */
    public function testCheckNavigator()
    {
        $dispatcher = new Dispatcher($this->request,$this->response);
        $this->setPrivateProperty($dispatcher,'config',[
            'controller_name' => 'no controller',
            'action_name' => 'and no action',
        ]);
        $this->assertNull($dispatcher->checkNavigator());
        $this->setPrivateProperty($dispatcher,'config',[
            'controller_name' => '\Bone\Mvc\Controller',
            'action_name' => 'and no action',
        ]);
        $this->assertNull($dispatcher->checkNavigator());
        $this->setPrivateProperty($dispatcher,'config',[
            'controller_name' => '\Bone\Mvc\Controller',
            'action_name' => 'init',
        ]);
    }

    /**
     * @throws \Bone\Filter\Exception
     */
    public function testGetResponseBody()
    {
        $plates = new PlatesEngine(__DIR__);
        Registry::ahoy()->set('templates','blah');
        $controller = new Controller($this->request);
        $dispatcher = new Dispatcher($this->request,$this->response);
        $this->setPrivateProperty($dispatcher,'controller',$controller);
        $this->setPrivateProperty($controller,'viewEngine',$plates);

        ob_start();
        $this->invokeMethod($dispatcher,'distributeBooty');
        $body = ob_end_clean();

        $this->assertTrue(is_string($body));
        $this->assertEquals("<h1>Layout Template</h1>\n<p><h1>404</h1></p>",$body);
    }

    /**
     * @throws \Bone\Filter\Exception
     */
    public function testFireCannons()
    {
        $plates = new PlatesEngine(__DIR__);
        Registry::ahoy()->set('templates','blah');

        Test::double('Bone\Mvc\Dispatcher',['checkNavigator' => null,'sinkingShip' => 'glurg']);

        $dispatcher = new Dispatcher($this->request,$this->response);
        $controller = new Controller($this->request);
        $controller->setHeader('rubber', 'chicken');
        $this->assertEquals('chicken', $controller->getHeader('rubber'));

        $this->setPrivateProperty($controller,'viewEngine',$plates);
        $this->setPrivateProperty($dispatcher,'controller',$controller);
        $config = [
            'controller_name' => 'Bone\Mvc\Controller',
            'action_name' => 'indexAction',
            'controller' => 'controller',
            'action' => 'index',
        ];
        $this->setPrivateProperty($dispatcher,'config',$config);

        ob_start();
        $dispatcher->fireCannons();
        $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals("<h1>Layout Template</h1>\n<p>Override this method</p>", $content);

        $dispatcher = new Dispatcher($this->request,$this->response);
        $controller = new Controller($this->request);
        $this->setPrivateProperty($dispatcher,'controller',$controller);
        $this->setPrivateProperty($dispatcher,'config',$config);

        $dispatcher->fireCannons();
    }


    /**
     * @param $object
     * @param $property
     * @param $value
     * @return mixed
     */
    public function setPrivateProperty(&$object, $property, $value)
    {
        $reflection = new ReflectionClass(get_class($object));
        $prop = $reflection->getProperty($property);
        $prop->setAccessible(true);
        $prop->setValue($object,$value);
        return $value;
    }


    /**
     * This method allows us to test protected and private methods without
     * having to go through everything using public methods
     *
     * @param object &$object
     * @param string $methodName
     * @param array  $parameters
     *
     * @return mixed could return anything!.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }

}
