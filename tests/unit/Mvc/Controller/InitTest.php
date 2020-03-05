<?php

namespace BoneTest\Mvc\Controller;

use Barnacle\Container;
use Bone\Controller\Controller;
use Bone\Controller\Init;
use Bone\View\ViewEngine;
use Bone\Server\SessionAwareInterface;
use Bone\Server\SiteConfig;
use Bone\Traits\HasSessionTrait;
use Codeception\TestCase\Test;
use Del\SessionManager;
use Laminas\I18n\Translator\Translator;

class InitTest extends Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testInit()
    {
        $controller = new class extends Controller implements SessionAwareInterface {
            use HasSessionTrait;
        };

        $container = new Container();
        $container[SiteConfig::class] = $this->getMockBuilder(SiteConfig::class)->disableOriginalConstructor()->getMock();
        $container[Translator::class] = $this->getMockBuilder(Translator::class)->getMock();
        $container[ViewEngine::class] = $this->getMockBuilder(ViewEngine::class)->getMock();
        $container[SessionManager::class] = SessionManager::getInstance();
        $controller = Init::controller($controller, $container);
        $this->assertInstanceOf(SiteConfig::class, $controller->getSiteConfig());
        $this->assertInstanceOf(Translator::class, $controller->getTranslator());
        $this->assertInstanceOf(ViewEngine::class, $controller->getView());
        $this->assertInstanceOf(SessionManager::class, $controller->getSession());
    }
}