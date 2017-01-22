<?php


use Bone\Mvc\Application;
use Zend\Diactoros\Response;
use AspectMock\Test;

class BoneMvcApplicationTest extends \Codeception\TestCase\Test
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
        $this->response->getBody()->write('All hands on deck!');
    }

    protected function _after()
    {
        Test::clean();
    }

    // make sure the feckin' headers are at hand
    public function testCanGetInstance()
    {
        $config = array(
            'routes' => array(
                '/' => array(
                    'controller' => 'index',
                    'action' => 'index',
                    'params' => array(),
                ),
            )
        );
        $this->assertInstanceOf('\Bone\Mvc\Application',Application::ahoy($config));
    }

    // make sure the feckin' ship sails
    public function testCanSetSail()
    {
        $config = array(
            'routes' => array(
                '/' => array(
                    'controller' => 'index',
                    'action' => 'index',
                    'params' => array(),
                ),
            )
        );
        Test::double('\Zend\Diactoros\ServerRequest',['getURI' => '/']);
        Test::double('\Bone\Mvc\Dispatcher',['fireCannons' => null]);
        $this->assertNull(Application::ahoy($config)->setSail());
    }



}


