<?php

use Bone\Mvc\Dispatcher;
use Bone\Mvc\Controller;
use Bone\Mvc\Registry;
use Psr\Http\Message\ServerRequestInterface ;
use Psr\Http\Message\ResponseInterface;
use AspectMock\Test;
use Zend\Diactoros\Response;

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


        $this->request = Test::double('\Zend\Diactoros\ServerRequest')->make();
//        $this->response = Test::double('\Zend\Diactoros\Response')->make();
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
        $loader = new Twig_Loader_String();
        $twig = new Twig_Environment($loader);
        Registry::ahoy()->set('templates','blah');
        $dispatcher = new Dispatcher($this->request,$this->response);
        $controller = new Controller($this->request);
        $this->setPrivateProperty($controller,'twig',$twig);
        $this->assertEquals('layouts/b.twig',$this->invokeMethod($dispatcher,'templateCheck',[$controller,'moreblah']));
    }


    public function testPlunderEnemyShip()
    {
        $loader = new Twig_Loader_String();
        $twig = new Twig_Environment($loader);
        Registry::ahoy()->set('templates','blah');

        $dispatcher = new Dispatcher($this->request,$this->response);
        $this->setPrivateProperty($dispatcher,'config',[
            'action_name' => 'init',
        ]);
        $controller = new Controller($this->request);
        $this->setPrivateProperty($controller,'twig',$twig);
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
        $this->assertEquals('500 Page Error.',$this->invokeMethod($dispatcher,'sinkingShip',['argh']));
    }


    /**
     *  check it be runnin through setting the destination
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


    public function testGetResponseBody()
    {
        $loader = new Twig_Loader_String();
        $twig = new Twig_Environment($loader);
        Registry::ahoy()->set('templates','blah');
        $controller = new Controller($this->request);
        $dispatcher = new Dispatcher($this->request,$this->response);
        $this->setPrivateProperty($dispatcher,'controller',$controller);
        $this->setPrivateProperty($controller,'twig',$twig);
        $body = $this->invokeMethod($dispatcher,'getResponseBody');
        $this->assertTrue(is_string($body));
        $this->assertEquals('layouts/b.twig',$body);
    }


    public function testFireCannons()
    {
        $loader = new Twig_Loader_String();
        $twig = new Twig_Environment($loader);
        Registry::ahoy()->set('templates','blah');

        Test::double('Bone\Mvc\Dispatcher',['checkNavigator' => null,'sinkingShip' => 'glurg']);

        $dispatcher = new Dispatcher($this->request,$this->response);
        $controller = new Controller($this->request);

        $this->setPrivateProperty($controller,'twig',$twig);
        $this->setPrivateProperty($dispatcher,'controller',$controller);
        $config = [
            'controller_name' => 'Bone\Mvc\Controller',
            'action_name' => 'init',
            'controller' => 'controller',
            'action' => 'init',
        ];
        $this->setPrivateProperty($dispatcher,'config',$config);
        $this->assertNull($dispatcher->fireCannons());


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