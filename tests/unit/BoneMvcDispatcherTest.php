<?php

use \Bone\Mvc\Dispatcher;
use \Bone\Mvc\Request;
use \Bone\Mvc\Response;
use \Bone\Mvc\Response\Headers;
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
//        $this->dispatcher = new Dispatcher($this->request,$this->response);
    }

    protected function _after()
    {
        Test::clean();
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
    }

}