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

    public function testGetAndSetParam()
    {
        $this->request->setParam('testing','hello');
        $this->tester->assertEquals($this->request->getParam('testing'), 'hello');
    }

    public function testGetAndSetParams()
    {
        $this->request->setParams(array(
            'name' => 'LeChuck',
            'occupation' => 'Ghost Pirate',
        ));
        $params = $this->request->getParams();
        $this->tester->assertTrue(is_array($params));
        $this->tester->assertTrue(count($params) == 2);
        $this->tester->assertEquals($params['name'],'LeChuck');
    }

    public function testGetAndSetController()
    {
        $this->request->setController('PirateController');
        $this->tester->assertTrue($this->request->getController(),'PirateController');
    }

    public function testGetAndSetAction()
    {
        $this->request->setAction('non-stop-action');
        $this->tester->assertEquals($this->request->getAction(),'non-stop-action');
    }

    public function testInvalidGetRawDataKey()
    {
        $this->tester->assertNull($this->request->getRawData('whats-all-this-then', 'non existent'));
    }

    public function testInvalidGetRawDataValue()
    {
        $this->tester->assertNull($this->request->getRawData('get', 'non existent value'));
    }

}
