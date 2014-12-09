<?php

use Bone\Filter;

class BoneFilterTest extends \Codeception\TestCase\Test
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

    // test object can be created 
    public function testCanFilterString()
    {
    	$this->assertEquals('boneMVC',Filter::filterString('bone-m-v-c','DashToCamelCase'));
    }

    // test it throws an exception
    public function testThrowsException()
    {
        try
        {
            Filter::filterString('test string','NonExistentFilter');
            $this->assertTrue(false);
        }
        catch(Exception $e)
        {
            $this->assertTrue(true);
        }
    }

}