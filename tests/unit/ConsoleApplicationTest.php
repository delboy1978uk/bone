<?php

use Bone\ConsoleApplication;
use Codeception\TestCase\Test;

class ConsoleApplicationTest extends Test
{

    public function testGetLongVersion()
    {
        $app = new ConsoleApplication();
        $this->assertTrue(is_string($app->getLongVersion()));
    }
}