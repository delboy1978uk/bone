<?php

use \Bone\Mvc\Request;

class BoneMvcRequestTest extends \Codeception\TestCase\Test
{
   /**
    * @var \UnitTester
    */
    protected $tester;

    protected $request;

    protected function _before()
    {

        $request = array();
        $get = array('getParam1' => 'getParam1Value');
        $post = array('postParam1' => 'postParam1Value');
        $cookie = array();
        $server = array();

        $this->request = new Request($request, $get, $post, $cookie, $server);

    }

    protected function _after()
    {
    }

    // tests
    public function testRequestConstruct()
    {
        $this->tester->assertNotEmpty($this->request->getGet());
        $this->tester->assertNotEmpty($this->request->getPost());
    }

    public function testRequestGetData()
    {
        $this->tester->assertEquals($this->request->getRawData('get', 'getParam1'), 'getParam1Value');
    }

    public function testRequestPostData()
    {
        $this->tester->assertEquals($this->request->getRawData('post', 'postParam1'), 'postParam1Value');
    }

}
