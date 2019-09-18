<?php

use Bone\Service\LoggerFactory;
use Codeception\TestCase\Test;

class LoggerTest extends Test
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


    /**
     * @throws Exception
     */
    public function testCreateLoggersThrowsEception()
    {
        $this->expectException(InvalidArgumentException::class);
        $config = [];
        $factory = new LoggerFactory();
        $factory->createLoggers($config);
    }
}