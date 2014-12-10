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

    protected $request;

    protected function _before()
    {
        Test::double('\Bone\Mvc\Request');
    }

    protected function _after()
    {
    }

    // test object can be created
    public function test()
    {

    }


}