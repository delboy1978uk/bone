<?php

use Bone\Log\LoggerFactory;
use Codeception\Test\Unit;

class LoggerTest extends Unit
{
    /**
     * @throws Exception
     */
    public function testCreateLoggers()
    {
        $config = [
            'channels' => [
                'default' => 'tests/_data/log/default_log',
            ],
        ];
        $factory = new LoggerFactory();
        $log = $factory->createLoggers($config);

        $this->assertTrue(is_array($log));
        $this->assertCount(1, $log);
        $this->assertInstanceOf('Monolog\Logger', $log['default']);
    }
}
