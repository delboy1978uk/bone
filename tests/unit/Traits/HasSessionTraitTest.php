<?php

use Bone\Traits\HasSessionTrait;
use Codeception\TestCase\Test;
use Del\SessionManager;

class HasSessionTraitTest extends Test
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