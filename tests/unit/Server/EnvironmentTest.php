<?php

use Bone\Server\Environment;
use Codeception\TestCase\Test;

class EnvironmentTest extends Test
{
    const CONFIG_FOLDER = 'tests/_data/config';

    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testCanGetLegacyConfig()
    {
        $server = new Environment([]);
        $config = $server->fetchConfig(self::CONFIG_FOLDER, '');
        $this->assertCount(2, $config);
        $this->assertArrayHasKey('legacy config', $config);
        $this->assertArrayHasKey('db', $config);
        $this->assertEquals('hello', $config['legacy config']);
        $this->assertEquals('whatever', $config['db']);
    }

    public function testCanGetDevConfig()
    {
        $server = new Environment([]);
        $config = $server->fetchConfig(self::CONFIG_FOLDER, 'development');
        die(var_dump($config));
        $this->assertCount(3, $config);
        $this->assertArrayHasKey('legacy config', $config);
        $this->assertArrayHasKey('development config', $config);
        $this->assertArrayHasKey('db', $config);
        $this->assertEquals('hello', $config['legacy config']);
        $this->assertEquals('db', $config['dev value']);
    }

    public function testCanGetProdConfig()
    {
        $server = new Environment([]);
        $config = $server->fetchConfig(self::CONFIG_FOLDER, 'production');
        $this->assertCount(3, $config);
        $this->assertArrayHasKey('legacy config', $config);
        $this->assertArrayHasKey('production config', $config);
        $this->assertArrayHasKey('db', $config);
        $this->assertEquals('hello', $config['legacy config']);
        $this->assertEquals('db', $config['production value']);
    }
}