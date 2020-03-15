<?php

use Bone\Traits\HasViewTrait;
use Bone\View\ViewEngine;
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

        $view = new ViewEngine();
        $class->setView($view);
        $this->assertInstanceOf(ViewEngine::class, $class->getView());
    }
}