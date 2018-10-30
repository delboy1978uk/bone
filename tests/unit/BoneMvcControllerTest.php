<?php

use AspectMock\Test;
use Bone\Mvc\Controller;
use Bone\Mvc\Registry;
use Bone\Mvc\View\PlatesEngine;
use Bone\Mvc\View\ViewEngine;
use Bone\Service\MailService;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Mail\Transport\Smtp;

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
        $request = new Request([], [], '/', 'POST', 'hello=world',
            ['Content-Type' => 'application/x-www-form-urlencoded'],
            [], [],
            ['hello' => 'world']
        );
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
        Test::double(PDO::class);
        Registry::ahoy()->set('db',[
            'host' => '127.0.0.1',
            'database' => 'bone_db',
            'user'     => 'travis',
            'pass'     => 'drinkgrog',
        ]);
        $db = $this->controller->getDbAdapter();
        $this->assertInstanceOf(PDO::class, $db);

    }

    /**
     * @throws Exception
     */
    public function testMailService()
    {
        Test::double(Smtp::class);
        Registry::ahoy()->set('mail', [
            'name' => 'test',
            'host' => 'test',
            'port' => 25,
        ]);
        $mail = $this->controller->getMailService();
        $this->assertInstanceOf(MailService::class, $mail);

    }

    public function testGetViewEngine()
    {
        $this->assertInstanceOf(ViewEngine::class, $this->controller->getViewEngine());
        $this->controller->setViewEngine(new PlatesEngine('.'));
        $this->assertInstanceOf(PlatesEngine::class, $this->controller->getViewEngine());
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
        $post = $this->controller->getPost();
        $this->assertTrue(is_array($this->controller->getPost()));
        $this->assertCount(1, $post);
        $this->assertArrayHasKey('hello', $post);
        $this->assertEquals('world', $post['hello']);
        $this->assertEquals('world', $this->controller->getPost('hello'));
    }


    public function testGetParam()
    {
        $this->assertEquals('rum',$this->controller->getParam('drink'));
        $this->controller->setParam('date', new DateTime());
        $this->assertInstanceOf(DateTime::class, $this->controller->getParam('date'));
        $this->assertEquals('fail', $this->controller->getParam('doesnteExist', 'fail'));
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