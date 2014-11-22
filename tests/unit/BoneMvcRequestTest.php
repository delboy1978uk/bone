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
        $get = array('getParam1' => 'getParam1Value');
        $post = array('postParam1' => 'postParam1Value');
        $cookie = array('cookieParam1' => 'cookieParam1Value');
        $server = array('serverParam1' => 'serverParam1Value');

        $this->request = new Request( $get, $post, $cookie, $server);

    }

    protected function _after()
    {
    }

    // tests
    public function testRequestConstruct()
    {
        $this->tester->assertNotEmpty($this->request->getGet());
        $this->tester->assertNotEmpty($this->request->getPost());
        $this->tester->assertNotEmpty($this->request->getCookie());
        $this->tester->assertNotEmpty($this->request->getServer());
    }

    public function testRequestGetData()
    {
        $this->tester->assertEquals($this->request->getRawData('get', 'getParam1'), 'getParam1Value');
    }

    public function testRequestPostData()
    {
        $this->tester->assertEquals($this->request->getRawData('post', 'postParam1'), 'postParam1Value');
    }

    public function testRequestCookieData()
    {
        $this->tester->assertEquals($this->request->getRawData('cookie', 'cookieParam1'), 'cookieParam1Value');
    }

    public function testRequestServerData()
    {
        $this->tester->assertEquals($this->request->getRawData('server', 'serverParam1'), 'serverParam1Value');
    }

}
