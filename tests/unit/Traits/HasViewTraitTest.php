<?php

use Bone\View\Traits\HasViewTrait;
use Bone\View\ViewEngine;
use Codeception\Test\Unit;

class HasViewTraitTest extends Unit
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

        $view = new ViewEngine();
        $class->setView($view);
        $this->assertInstanceOf(ViewEngine::class, $class->getView());
    }
}
