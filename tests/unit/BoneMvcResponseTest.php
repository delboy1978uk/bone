<?php

use \Bone\Mvc\Response;
use \Bone\Mvc\Response\Headers;

class BoneMvcResponseTest extends \Codeception\TestCase\Test
{
   /**
    * @var \UnitTester
    */
    protected $tester;

    /** @var Response */
    protected $response;

    protected function _before()
    {
        $this->response = new Response();
        $this->response->setBody('All hands on deck!');
        $this->response->setHeaders(new Headers());
    }

    protected function _after()
    {
    }

    // make sure the feckin' headers are at hand
    public function testCanGetAndSetHeaders()
    {
        $this->assertInstanceOf('\Bone\Mvc\Response\Headers',$this->response->getHeaders());
    }

    // make sure the feckin' body is at hand
    public function testCanGetAndSetBody()
    {
        $this->assertEquals('All hands on deck!',$this->response->getBody());
    }

    // th' feckin' end point in the app
    public function testSend()
    {
        $this->assertNull($this->response->send());
    }

}