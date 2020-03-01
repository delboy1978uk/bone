<?php

use Bone\Traits\HasViewTrait;
use Bone\View\PlatesEngine;
use Codeception\TestCase\Test;

class HasViewTraitTest extends Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testGetSet()
    {
        $class = new class {
            use HasViewTrait;
        };

        $view = new PlatesEngine();
        $class->setView($view);
        $this->assertInstanceOf(PlatesEngine::class, $class->getView());
    }
}