<?php

use Bone\Mvc\Dispatcher;
use Bone\Mvc\Controller;
use Bone\Mvc\Registry;
use Bone\Mvc\View\PlatesEngine;
use Bone\Server\Environment;
use Codeception\TestCase\Test;
use Psr\Http\Message\ServerRequestInterface ;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\TextResponse;
use Zend\Diactoros\ServerRequest;

class BoneMvcDispatcherTest extends Test
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
        $this->request = new ServerRequest();
        $this->response = new Response();
    }

    protected function _after()
    {
        unset($this->request);
        unset($this->response);
    }

    /**
     * @throws Exception
     */
    public function testCheckControllerExists()
    {
        // check for an obviouslly existant class
        $dispatcher = new Dispatcher($this->request,$this->response, new Environment([]));
        $this->setPrivateProperty($dispatcher,'config',[
            'controller_name' => 'DateTime'
        ]);
        $this->assertTrue($this->invokeMethod($dispatcher,'checkControllerExists'));
    }

    /**
     * @throws Exception
     */
    public function testCheckActionExists()
    {
        // check for an obviously existent class
        $dispatcher = new Dispatcher($this->request,$this->response, new Environment([]));
        $this->setPrivateProperty($dispatcher,'controller',new DateTime());
        $this->setPrivateProperty($dispatcher,'config',[
            'action_name' => 'modify',
        ]);
        $this->assertTrue($this->invokeMethod($dispatcher,'checkActionExists'));
    }

    /**
     * @throws Exception
     */
    public function testSetNotFound()
    {
        $dispatcher = new Dispatcher($this->request,$this->response, new Environment([]));
        $this->assertNull($this->invokeMethod($dispatcher,'setNotFound'));
    }

    /**
     * @throws Exception
     */
    public function testTemplateCheck()
    {
        $plates = new PlatesEngine(__DIR__.DIRECTORY_SEPARATOR);
        Registry::ahoy()->set('templates','blah');
        $dispatcher = new Dispatcher($this->request,$this->response, new Environment([]));
        $controller = new Controller($this->request);
        $this->setPrivateProperty($controller,'viewEngine',$plates);
        $this->assertEquals("<h1>Layout Template</h1>\n<p>moreblah</p>",$this->invokeMethod($dispatcher,'templateCheck',[$controller,'moreblah']));
    }

    /**
     * @throws Exception
     */
    public function testPlunderEnemyShip()
    {
        $plates = new PlatesEngine(__DIR__.DIRECTORY_SEPARATOR);
        Registry::ahoy()->set('templates','blah');

        $dispatcher = new Dispatcher($this->request,$this->response, new Environment([]));
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
     *
     * @throws Exception
     */
    public function testSinkingShip()
    {
        Registry::ahoy()->set('templates', null);
        $dispatcher = new Dispatcher($this->request,$this->response, new Environment([]));
        $this->invokeMethod($dispatcher,'sinkingShip',[new Bone\Exception('argh')]);
        $reflection = new ReflectionClass(get_class($dispatcher));
        $prop = $reflection->getProperty('controller');
        $prop->setAccessible(true);
        $controller = $prop->getValue($dispatcher);
        $body = $controller->getBody();
        $this->assertEquals('500 Page Error.', $body);
    }

    /**
     * @throws Exception
     */
    public function testGetTemplateName()
    {
        $dispatcher = new Dispatcher($this->request,$this->response, new Environment([]));
        $output = $this->invokeMethod($dispatcher, 'getTemplateName', [null]);
        $this->assertNull($output);
        $output = $this->invokeMethod($dispatcher, 'getTemplateName', ['pirated-template']);
        $this->assertEquals('pirated-template', $output);
        $output = $this->invokeMethod($dispatcher, 'getTemplateName', [['pirated-template']]);
        $this->assertEquals('pirated-template', $output);
    }


    /**
     *  check it be runnin through setting the destination
     *
     * @throws Exception
     */
    public function testCheckNavigator()
    {
        $dispatcher = new Dispatcher($this->request,$this->response, new Environment([]));
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
     * @throws Exception
     */
    public function testGetResponseBody()
    {
        $plates = new PlatesEngine(__DIR__);
        Registry::ahoy()->set('templates','blah');
        $controller = new Controller($this->request);
        $dispatcher = new Dispatcher($this->request,$this->response, new Environment([]));
        $this->setPrivateProperty($dispatcher,'controller', $controller);
        $this->setPrivateProperty($controller,'viewEngine', $plates);

        ob_start();
        $this->invokeMethod($dispatcher,'distributeBooty');
        $body = ob_get_contents();
        ob_end_clean();

        $this->assertTrue(is_string($body));
        $this->assertEquals("<h1>Layout Template</h1>\n<p><h1>404</h1></p>",$body);
    }

    /**
     * @throws Exception
     */
    public function testFireCannons()
    {

        $plates = new PlatesEngine(__DIR__);
        Registry::ahoy()->set('templates', 'blah');
        $dispatcher = $this->createMock(Dispatcher::class);
        $dispatcher->method('checkNavigator')->willReturn(null);
        $dispatcher->method('sinkingShip')->willReturn('glurg');

        $dispatcher = new Dispatcher($this->request,$this->response, new Environment([]));
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

        $this->assertNotEmpty($content);

    }

    /**
     * @throws Exception
     */
    public function testDistributeBootySendsResponse()
    {
        $controller = new Controller($this->request);
        $controller->view = new TextResponse('Message in a bottle!');
        $dispatcher = new Dispatcher($this->request, $this->response, new Environment([]));
        $this->setPrivateProperty($dispatcher,'controller', $controller);

        $plates = new PlatesEngine(__DIR__);
        Registry::ahoy()->set('templates','blah');

        $this->setPrivateProperty($controller,'viewEngine',$plates);
        $this->setPrivateProperty($dispatcher,'controller',$controller);
        $config = [
            'action_name' => 'errorAction',
        ];
        $this->setPrivateProperty($dispatcher,'config',$config);

        ob_start();
        $this->invokeMethod($dispatcher, 'distributeBooty');
        $body = ob_get_contents();
        ob_end_clean();

        $this->assertTrue(is_string($body));
        $this->assertEquals('Message in a bottle!', $body);
    }

    /**
     * @throws Exception
     */
    public function testSetStatusCode()
    {
        $controller = new Controller($this->request);
        $controller->setStatusCode(700);
        $dispatcher = new Dispatcher($this->request,$this->response, new Environment([]));
        $this->setPrivateProperty($dispatcher,'controller', $controller);
        $this->invokeMethod($dispatcher, 'setStatusCode');
        $reflection = new ReflectionClass(Dispatcher::class);
        $prop = $reflection->getProperty('response');
        $prop->setAccessible(true);
        $response = $prop->getValue($dispatcher);
        $this->assertInstanceOf(Response::class, $response);
    }

    /**
     * @throws Exception
     */
    public function testSetHeaders()
    {
        $controller = new Controller($this->request);
        $controller->setHeaders(['Content-Type' => 'application/json']);
        $dispatcher = new Dispatcher($this->request,$this->response, new Environment([]));
        $this->setPrivateProperty($dispatcher,'controller', $controller);
        $this->invokeMethod($dispatcher, 'setHeaders');
        $reflection = new ReflectionClass(Dispatcher::class);
        $prop = $reflection->getProperty('response');
        $prop->setAccessible(true);
        $response = $prop->getValue($dispatcher);
        /** @var Response $response */
        $this->assertInstanceOf(Response::class, $response);
        $this->assertArrayHasKey('Content-Type', $response->getHeaders());
    }

    /**
     * @throws Exception
     */
    public function testPlunderEnemyShipSetsReturnedResponse()
    {
        $controller = new Controller($this->request);
        $dispatcher = new Dispatcher($this->request, new Response(), new Environment([]));

        $config = [
            'action_name' => 'errorAction',
        ];
        $this->setPrivateProperty($dispatcher,'config', $config);
        $this->setPrivateProperty($dispatcher,'controller', $controller);
        $this->invokeMethod($dispatcher, 'plunderEnemyShip');

        $reflection = new ReflectionClass(Dispatcher::class);
        $prop = $reflection->getProperty('controller');
        $prop->setAccessible(true);

        $controller = $prop->getValue($dispatcher);
        $this->assertInstanceOf(TextResponse::class, $controller->view);
    }


    /**
     * @param $object
     * @param $property
     * @param $value
     * @return mixed
     * @throws ReflectionException
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
     * @throws ReflectionException
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }

}
