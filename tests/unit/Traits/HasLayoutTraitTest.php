<?php

use Bone\Router\Traits\HasLayoutTrait;
use Codeception\TestCase\Test;

class HasLayoutTraitTest extends Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testGetSet()
    {
        $class = new class {
            use HasLayoutTrait;
        };

        $class->setLayout('some-layout');
        $this->assertEquals('some-layout', $class->getLayout());
    }
}