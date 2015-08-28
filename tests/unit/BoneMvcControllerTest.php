<?php

use AspectMock\Test;
use Bone\Mvc\Controller;
use Bone\Mvc\Registry;
use Bone\Mvc\Request;
use Bone\Mvc\Response\Headers;

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
        Test::double(new Request([],[],[],[]), ['getParams' => new StdClass(),'getParam' => 'rum']);
        Test::double(new Twig_Loader_Filesystem(),['__construct' => null]);
        Test::double(new Twig_Environment(),['__construct' => null, 'addExtension' => null]);
        Test::double('Twig_Extension_Debug');
        $this->controller = new Controller(new Request([],[],[],[])) ;
    }

    protected function _after()
    {
        Test::clean();
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
        Test::double('Bone\Db\Adapter\MySQL',[
            'getHost' => '127.0.0.1',
            'getDatabase' => 'bone_db',
            'getUser'     => 'travis',
            'getPass'     => 'drinkgrog',
        ]);
        try{
            $db = $this->controller->getDbAdapter();
            $this->assertInstanceOf('PDO',$db);
        }catch(\Exception $e){die(var_dump($e));}

    }



    public function testGetTwig()
    {
//        $this->assertInstanceOf('Twig_Environment',$this->controller->getTwig());
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
//        $this->assertInstanceOf('\Bone\Mvc\Response\Headers',$this->controller->getHeaders());
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