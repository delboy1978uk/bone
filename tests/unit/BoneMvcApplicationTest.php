<?php


use Bone\Mvc\Application;
use BoneMvc\Module\App\AppPackage;
use BoneMvc\Module\BoneMvcDoctrine\BoneMvcDoctrinePackage;
use BoneMvc\Module\BoneMvcUser\BoneMvcUserPackage;
use BoneTest\TestPackage\TestPackagePackage;
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

    /**
     *
     */
    public function testCanGetInstance()
    {
        $app = Application::ahoy();
        $this->assertInstanceOf(Application::class, $app);
    }


    /**
     * @throws Exception
     */
    public function testGetContainer()
    {
        $application = Application::ahoy();
        $container = $application->getContainer();
        $this->assertInstanceOf('Barnacle\Container', $container);
    }

    /**
     * @throws Exception
     */
    public function testCanSetSail()
    {
        global $_SERVER;
        $application = Application::ahoy();
        $application->setConfigFolder('tests/_data/config');
        $_SERVER = [
            'REQUEST_URI' => '/en_GB/testpackage'
        ];
        ob_start();
        $this->assertTrue($application->setSail());
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('<!DOCTYPE html><html lang="en"><head></head><body><h1>Template</h1><h3>Content Below</h3><h1>TestPackage</h1><p class="lead">Lorem ipsum dolor sit amet</p></body></html>', $contents);
    }

    /**
     * @throws Exception
     */
    public function testMethodNotAllowed()
    {
        global $_SERVER;
        $application = Application::ahoy();
        $application->setConfigFolder('tests/_data/config');
        $_SERVER = [
            'REQUEST_URI' => '/en_GB/testpackage',
            'REQUEST_METHOD' => 'POST',
        ];
        ob_start();
        $this->assertTrue($application->setSail());
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('<!DOCTYPE html><html lang="en"><head></head><body><h1>Template</h1><h3>Content Below</h3><section class="intro">
    <div class="intro-body">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <img src="/img/skull_and_crossbones.png" />
                    <h1 class="brand-heading">405</h1>
                    <p class="intro-text">Method not allowed.</p>
                </div>
            </div>
        </div>
    </div>
</section></body></html>', $contents);
    }

    /**
     * @throws Exception
     */
    public function testApi()
    {
        global $_SERVER;
        $application = Application::ahoy();
        $application->setConfigFolder('tests/_data/config');
        $_SERVER = [
            'REQUEST_URI' => '/en_GB/api/testpackage',
        ];
        ob_start();
        $this->assertTrue($application->setSail());
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('{"drink":"grog","pieces":"of eight","shiver":"me timbers"}', $contents);
    }

    /**
     * @throws Exception
     */
    public function testException()
    {
        global $_SERVER;
        $application = Application::ahoy();
        $application->setConfigFolder('tests/_data/config');
        $_SERVER = [
            'REQUEST_URI' => '/en_GB/bad',
        ];
        ob_start();
        $this->assertTrue($application->setSail());
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assertContains('<h1 class="brand-heading">Shiver Me Timbers</h1>', $contents);
        $this->assertContains('Garrrr! It be the redcoats!', $contents);
    }

    /**
     * @throws Exception
     */
    public function testJsonResponse()
    {
        global $_SERVER;
        $application = Application::ahoy();
        $application->setConfigFolder('tests/_data/config');
        $_SERVER = [
            'REQUEST_URI' => '/en_GB/another',
        ];
        ob_start();
        $this->assertTrue($application->setSail());
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('{"drink":"grog","pieces":"of eight","shiver":"me timbers"}', $contents);
    }

    /**
     * @throws Exception
     */
    public function test404Request()
    {
        global $_SERVER;
        $application = Application::ahoy();
        $application->setConfigFolder('tests/_data/config');
        $_SERVER = [
            'REQUEST_URI' => '/en_GB/blistering-barnacles'
        ];
        ob_start();
        $this->assertTrue($application->setSail());
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('<!DOCTYPE html><html lang="en"><head></head><body><h1>Template</h1><h3>Content Below</h3>Th\' page canna be found, Cap\'n.</body></html>', $contents);
    }



    /**
     * @throws Exception
     */
    public function testRedirectToLocalisedURL()
    {
        global $_SERVER;
        $application = Application::ahoy();
        $application->setConfigFolder('tests/_data/config');
        $_SERVER = [
            'REQUEST_URI' => '/testpackage'
        ];
        ob_start();
        $this->assertTrue($application->setSail());
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('', $contents);
    }



    /**
     * @throws Exception
     */
    public function testWithoutI18n()
    {
        global $_SERVER;
        $application = Application::ahoy();
        $application->setConfigFolder('tests/_data/config2');
        $_SERVER = [
            'REQUEST_URI' => '/testpackage'
        ];
        ob_start();
        $this->assertTrue($application->setSail());
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('<!DOCTYPE html><html lang="en"><head></head><body><h1>Template</h1><h3>Content Below</h3><h1>TestPackage</h1><p class="lead">Lorem ipsum dolor sit amet</p></body></html>', $contents);
    }
}


