<?php

use Bone\Mvc\ControllerFactory;
use Codeception\Util\Stub;

class BoneMvcControllerFactoryTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $request;

    protected function _before()
    {
        $this->request = Stub::make('\Bone\Mvc\Request');

    }

    protected function _after()
    {
    }

    // test object can be created 
    public function testCreateController()
    {
        define('APPLICATION_PATH', realpath(__DIR__ . '/../'));
        $factory = new ControllerFactory();
//        $controller = $factory->create('\Bone\Mvc\Controller',$this->request);
//        $this->assertInstanceOf('\Bone\Mvc\Controller',$controller);
    }


}