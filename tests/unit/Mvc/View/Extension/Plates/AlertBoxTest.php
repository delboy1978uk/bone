<?php

namespace BoneTest\Mvc\View\Extension\Plates;

use Bone\Mvc\View\Extension\Plates\AlertBox;
use Codeception\TestCase\Test;

class AlertBoxTest extends Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;


    public function testAlertBox()
    {
        $viewHelper = new AlertBox();
        $this->assertEquals('<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>hello</div>', $viewHelper->alertBox(['hello', 'info']));
    }
}