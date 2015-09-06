<?php

use Bone\Mvc\Dispatcher;
use Bone\Mvc\Controller;
use Bone\Mvc\Request;
use Bone\Mvc\Response;
use Bone\Mvc\Registry;
use Bone\Mvc\Response\Headers;
use AspectMock\Test;

class BoneMvcDispatcherTest extends \Codeception\TestCase\Test
{
   /**
    * @var \UnitTester
    */
    protected $tester;

    /** @var Request */
    protected $request;

    /** @var Response */
    protected $response;

    /** @var Dispatcher */
    protected $dispatcher;

    protected function _before()
    {
        Test::spec('\App\Controller\ErrorController');


        $this->request = Test::double('\Bone\Mvc\Request', array('getController' => 'index','getAction' => 'index'))->make();
        $this->response = Test::double('\Bone\Mvc\Response')->make();
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
        $this->setPrivateProperty($controller,'_twig',$twig);
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
        $this->setPrivateProperty($controller,'_twig',$twig);
        $this->setPrivateProperty($dispatcher,'controller',$controller);
        $this->assertNull($this->invokeMethod($dispatcher,'plunderEnemyShip'));
    }


    /**
     *  check it be runnin through setting the destination
     */
    public function testSinkingShip()
    {
        $dispatcher = new Dispatcher($this->request,$this->response);
        $this->assertNull($this->invokeMethod($dispatcher,'sinkingShip',['argh']));
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
        $this->setPrivateProperty($controller,'_twig',$twig);
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

        $headers = new Headers();
        $dispatcher = new Dispatcher($this->request,$this->response);
        $controller = new Controller($this->request);

        $this->setPrivateProperty($controller,'headers',$headers);
        $this->setPrivateProperty($controller,'_twig',$twig);
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