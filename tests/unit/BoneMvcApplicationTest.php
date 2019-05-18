<?php


use Bone\Mvc\Application;
use Codeception\TestCase\Test;
use Zend\Diactoros\Response;

class BoneMvcApplicationTest extends Test
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

    }

    /**
     *
     */
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
        $this->assertInstanceOf(Application::class, Application::ahoy($config));
    }

    /**
     * @throws Exception
     */
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
        $application = Application::ahoy($config);
        $this->assertTrue($application->setSail());
    }

}


