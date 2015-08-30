<?php

use Bone\Mvc\Dispatcher;
use Bone\Mvc\Request;
use Bone\Mvc\Response;
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
        // check for an obviouslly existant class
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


    /**
     *  check it be runnin through setting the destination
     */
    public function testValidateDestination()
    {
        Test::double('\Bone\Mvc\Request', array('getController' => 'index','getAction' => 'index'));
        Test::spec('\App\Controller\ErrorController');
//        $this->assertNull($this->dispatcher->validateDestination());
    }


    /**
     *  @todo how the feck do we test this?
     */
    public function testFireCannons()
    {
//        $this->dispatcher = new Dispatcher($this->request,$this->response);

//        $this->dispatcher = null;
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