<?php


use Bone\Mvc\Application;
use Bone\Mvc\Response;
use Bone\Mvc\Response\Headers;

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
        $this->response->setBody('All hands on deck!');
        $this->response->setHeaders(new Headers());
    }

    protected function _after()
    {
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

}