<?php

use Bone\Mvc\Response\Headers;

class BoneMvcResponseHeadersTest extends \Codeception\TestCase\Test
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

    // test getter/setter
    public function testGetAndSetHeaders()
    {
    	$headers = new Headers();
    	$headers->setHeader('Content-Type','application/json');
        $this->assertEquals('application/json',$headers->getHeader('Content-Type'));
    }

    // test non existent keys return false 
    public function testGetEmptyHeaderReturnsFalse()
    {
    	$headers = new Headers();
        $this->assertFalse($headers->getHeader('Content-Type'));
    }

    // test to array returns an array
    public function testToArrayReturnsArray()
    {
    	$headers = new Headers();
        $this->assertTrue(is_array($headers->toArray()));
    }

    // test json headers get set
    public function testSetJsonResponse()
    {
        $headers = new Headers();
        $headers->setJsonResponse();
        $this->assertEquals('application/json',$headers->getHeader('Content-Type'));
        $this->assertEquals('Mon, 26 Jul 1997 05:00:00 GMT',$headers->getHeader('Expires'));
        $this->assertEquals('no-cache, must-revalidate',$headers->getHeader('Cache-Control'));
    }

    // test headers send
    public function testDispatch()
    {
        $headers = new Headers();
        $headers->setHeader('Content-Type','text/html');
        $this->assertTrue($headers->dispatch());
    }
}