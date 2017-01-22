<?php

use AspectMock\Test;
use Bone\Mvc\Controller;
use Zend\Diactoros\ServerRequest as Request;

class BoneMvcControllerTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Controller $controller
     */
    protected $controller;

    protected function _before()
    {
        $request = new Request();
        $this->controller = new Controller($request) ;
        $this->controller->setParam('drink','rum');
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
        $db = $this->controller->getDbAdapter();
        $this->assertInstanceOf('PDO',$db);

    }



    public function testGetTwig()
    {
        $this->assertInstanceOf('Twig_Environment',$this->controller->getTwig());
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
        $head = $this->controller->getHeaders();
        $this->assertTrue(is_array($head));
    }


    public function testGetParams()
    {
        $this->assertInstanceOf('StdClass',$this->controller->getParams());
    }


    public function testGetParam()
    {
        $this->assertEquals('rum',$this->controller->getParam('drink'));
    }


    public function testNotFoundAction()
    {
        $this->assertNull($this->invokeMethod($this->controller, 'notFoundAction', []));
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