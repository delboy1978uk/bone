<?php

use AspectMock\Test;
use Bone\Db\Adapter\MySQL;
use Bone\Mvc\Controller;
use Bone\Mvc\View\ViewEngine;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequest as Request;

class BoneMvcControllerTest extends \Codeception\TestCase\Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @var Controller $controller
     */
    protected $controller;

    protected function _before()
    {
        $request = new Request();
        $this->controller = new Controller($request) ;
        $this->controller->init();
        $this->controller->postDispatch();
        $this->controller->setParam('drink','rum');
    }

    protected function _after()
    {
        Test::clean();
    }

    /**
     * @throws Exception
     */
    public function testGetDbAdapter()
    {
        Test::double(MySQL::class,[
            'getHost' => '127.0.0.1',
            'getDatabase' => 'bone_db',
            'getUser'     => 'travis',
            'getPass'     => 'drinkgrog',
        ]);
        $db = $this->controller->getDbAdapter();
        $this->assertInstanceOf(PDO::class, $db);

    }



    public function testGetViewEngine()
    {
        $this->assertInstanceOf(ViewEngine::class, $this->controller->getViewEngine());
    }


    public function testEnableDisableLayout()
    {
        $this->controller->disableLayout();
        $this->assertFalse($this->controller->hasLayoutEnabled());
        $this->controller->enableLayout();
        $this->assertTrue($this->controller->hasLayoutEnabled());
    }


    public function testEnableDisableView()
    {
        $this->controller->disableView();
        $this->assertFalse($this->controller->hasViewEnabled());
        $this->controller->enableView();
        $this->assertTrue($this->controller->hasViewEnabled());
    }


    public function testGetSetBody()
    {
        $this->controller->setBody('garrr! there be mermaids!');
        $this->assertEquals('garrr! there be mermaids!',$this->controller->getBody());
    }


    public function testGetHeaders()
    {
        $head = $this->controller->getHeaders();
        $this->assertTrue(is_array($head));
    }


    public function testGetSetHeader()
    {
        $this->controller->setHeader('monkey', 'island');
        $this->assertEquals('island', $this->controller->getHeader('monkey'));
    }


    public function testGetSetHeaders()
    {
        $this->controller->setHeaders(['monkey' => 'island', 'sword' => 'master']);
        $this->assertEquals('island', $this->controller->getHeader('monkey'));
        $this->assertEquals('master', $this->controller->getHeader('sword'));
        $headers = $this->controller->getHeaders();
        $this->assertTrue(is_array($headers));
        $this->assertCount(2, $headers);
    }


    public function testGetSetRequest()
    {
        $this->controller->setRequest(new Request());
        $this->assertInstanceOf(ServerRequestInterface::class, $this->controller->getRequest());
    }


    public function testGetParams()
    {
        $this->assertTrue(is_array($this->controller->getParams()));
    }


    public function testGetPost()
    {
        $this->assertTrue(is_array($this->controller->getPost()));
    }


    public function testGetParam()
    {
        $this->assertEquals('rum',$this->controller->getParam('drink'));
    }

    /**
     * @throws ReflectionException
     */
    public function testNotFoundAction()
    {
        $this->assertNull($this->invokeMethod($this->controller, 'notFoundAction', []));
    }

    public function testSendJsonResponse()
    {
        $data = [
            'drink' => 'grog',
            'sail' => 'the 7 seas',
        ];
        $this->controller->sendJsonResponse($data);
        $body = $this->controller->getBody();
        $this->assertEquals('{"drink":"grog","sail":"the 7 seas"}', $body);
    }


    /**
     * This method allows us to test protected and private methods without
     * having to go through everything using public methods
     *
     * @param object &$object
     * @param string $methodName
     * @param array  $parameters
     * @throws ReflectionException
     *
     * @return mixed could return anything!.
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}