<?php

use \Bone\Mvc\Request;

class BoneMvcRequestTest extends \Codeception\TestCase\Test
{
   /**
    * @var \UnitTester
    */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    // tests
    public function testMe()
    {
        $request = new Request($_REQUEST,$_GET,$_POST,$_COOKIE,$_SERVER);

//        $this->assertTrue($request->);

    }

}