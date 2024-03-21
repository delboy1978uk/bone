<?php

use Bone\Console\ConsoleApplication;
use Codeception\Test\Unit;

class ConsoleApplicationTest extends Unit
{

    public function testGetLongVersion()
    {
        $app = new ConsoleApplication();
        $this->assertTrue(is_string($app->getLongVersion()));
    }
}
