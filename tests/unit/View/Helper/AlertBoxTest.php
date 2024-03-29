<?php

use Bone\View\Helper\AlertBox;
use Codeception\Test\Unit;

class AlertBoxTest extends Unit
{
    /**
     * @throws Exception
     */
    public function testCreateAlertBox()
    {
        $helper = new AlertBox();
        $this->assertEquals('<div class="alert alert-info"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>crack open the rum</div>', $helper->alertBox(['crack open the rum']));
        $this->assertEquals('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>not one message<br />but two!</div>', $helper->alertBox([
            'not one message',
            'but two!',
            'danger',
        ]));
    }
}
