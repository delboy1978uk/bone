<?php

use Bone\Server\Traits\HasSessionTrait;
use Codeception\Test\Unit;
use Del\SessionManager;

class HasSessionTraitTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testGetSet()
    {
        $class = new class {
            use HasSessionTrait;
        };

        $session = SessionManager::getInstance();
        $class->setSession($session);
        $this->assertInstanceOf(SessionManager::class, $class->getSession());
    }
}
