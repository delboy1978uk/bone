<?php

use Bone\Mvc\ControllerFactory;
use Codeception\TestCase\Test;
use Codeception\Util\Stub;


class BoneMvcControllerFactoryTest extends Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected $request;

    protected function _before()
    {
        $this->request = Stub::make('\Zend\Diactoros\ServerRequest');
    }

    protected function _after()
    {

    }

    /**
     * test the feckin' object can be created
     *
     * @throws \Bone\Exception
     */
    public function testCreateController()
    {
        $factory = new ControllerFactory();
        $controller = $factory->create('\Bone\Mvc\Controller',$this->request);
        $this->assertInstanceOf('\Bone\Mvc\Controller',$controller);
    }

    /**
     * test throws a feckin' wobbly
     *
     * @throws \Bone\Exception
     */
    public function testThrowsException()
    {

        $factory = new ControllerFactory();
        $this->expectException('Exception');
        $factory->create('\Some\Inferior\Controller',$this->request);
    }
}