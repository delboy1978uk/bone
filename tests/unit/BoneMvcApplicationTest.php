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
        try {
            $app =  Application::ahoy();
            $this->assertInstanceOf(Application::class, $app);
        } catch (Exception $e) {
            codecept_debug($e);
        }

    }

    /**
     * @throws Exception
     */
    public function testCanSetSail()
    {
//        $config = array(
//            'routes' => array(
//                '/' => array(
//                    'controller' => 'index',
//                    'action' => 'index',
//                    'params' => array(),
//                ),
//            )
//        );
//        $application = Application::ahoy();
//        $this->assertTrue($application->setSail());
    }

}


