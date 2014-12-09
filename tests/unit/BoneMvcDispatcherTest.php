<?php

use \Bone\Mvc\Dispatcher;
use \Bone\Mvc\Request;
use \Bone\Mvc\Response;
use Codeception\Util\Stub;

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

        $get = array('getParam1' => 'getParam1Value');
        $post = array('postParam1' => 'postParam1Value');
        $cookie = array('cookieParam1' => 'cookieParam1Value');
        $server = array('serverParam1' => 'serverParam1Value');
        $this->request = new Request( $get, $post, $cookie, $server);
        $this->response = Stub::make('\Bone\Mvc\Response');
    }

    protected function _after()
    {
    }

    /**
     *  @todo how the feck do we test this?
     */
    public function testDispatcherInstantiation()
    {
//        $this->dispatcher = new Dispatcher($this->request,$this->response); // this throws an exception?!
//        $this->assertInstanceOf('Bone\Mvc\Dispatcher',$this->dispatcher);
    }

    /**
     *  @todo how the feck do we test this?
     */
    public function testValidateDestination()
    {

    }


    /**
     *  @todo how the feck do we test this?
     */
    public function testFireCannons()
    {

    }

}