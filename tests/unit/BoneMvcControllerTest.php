<?php

use AspectMock\Test;
use Bone\Mvc\Controller;
use Bone\Mvc\Request;

class BoneMvcControllerTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Controller
     */
    protected $controller;

    protected function _before()
    {
        Test::double('\Bone\Mvc\Request', ['getParams' => new StdClass(),'getParam' => 'rum']);
        Test::double('PDO');
        Test::double('\Twig_Loader_Filesystem',['setPaths' => null]);
        Test::double('\Twig_Environment');
        Test::double('\Twig_Extension_Debug');
        $this->controller = Test::double(new Controller(new Request(array(),array(),array(),array())) );
    }

    protected function _after()
    {

    }


    public function testInit()
    {
        $this->assertNull($this->controller->init());
    }


    public function testPostDispatch()
    {
        $this->assertNull($this->controller->postDispatch());
    }


    public function testGetDbAdapter()
    {
        $this->assertInstanceOf('PDO',$this->controller->getDbAdapter());
    }


    public function testGetTwig()
    {
//        $this->assertInstanceOf('\Twig_Environment',$this->controller->getTwig());
    }


    public function testEnableDisableLayout()
    {
        $this->controller->disableLayout();
        $this->assertFalse($this->controller->hasLayoutEnabled());
        $this->controller->enableLayout();
        $this->assertTrue($this->controller->hasLayoutEnabled());
    }


    public function testEnableDisableView()
    {
        $this->controller->disableView();
        $this->assertFalse($this->controller->hasViewEnabled());
        $this->controller->enableView();
        $this->assertTrue($this->controller->hasViewEnabled());
    }


    public function testGetSetBody()
    {
        $this->controller->setBody('garrr! there be mermaids!');
        $this->assertEquals('garrr! there be mermaids!',$this->controller->getBody());
    }


    public function testGetHeaders()
    {
        $this->assertInstanceOf('\Bone\Mvc\Response\Headers',$this->controller->getHeaders());
    }


    public function testGetParams()
    {
        $this->assertInstanceOf('StdClass',$this->controller->getParams());
    }


    public function testGetParam()
    {
        $this->assertEquals('rum',$this->controller->getParam('drink'));
    }


}