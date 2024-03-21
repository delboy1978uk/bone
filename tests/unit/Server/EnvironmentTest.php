<?php

use Bone\Server\Environment;
use Codeception\Test\Unit;

class EnvironmentTest extends Unit
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
        $this->assertTrue(is_array($config));
        $this->assertArrayHasKey('legacy config', $config);
        $this->assertArrayHasKey('db', $config);
        $this->assertEquals('hello', $config['legacy config']);
        $this->assertTrue(is_array($config['db']));
    }

    public function testCanGetDevConfig()
    {
        $server = new Environment([]);
        $config = $server->fetchConfig(self::CONFIG_FOLDER, 'development');
        $this->assertTrue(is_array($config));
        $this->assertArrayHasKey('legacy config', $config);
        $this->assertArrayHasKey('development config', $config);
        $this->assertArrayHasKey('db', $config);
        $this->assertEquals('hello', $config['legacy config']);
        $this->assertTrue(is_array($config['db']));
    }

    public function testCanGetProdConfig()
    {
        $server = new Environment([]);
        $config = $server->fetchConfig( self::CONFIG_FOLDER, 'production');
        $this->assertTrue(is_array($config));
        $this->assertArrayHasKey('legacy config', $config);
        $this->assertArrayHasKey('production config', $config);
        $this->assertArrayHasKey('db', $config);
        $this->assertEquals('hello', $config['legacy config']);
        $this->assertEquals('production value', $config['db']);
    }

    public function testGetters()
    {
        $json = file_get_contents('tests/_data/server.json');
        $server = json_decode($json, true);
        $env = new Environment($server);
        $this->assertEquals('https://awesome.scot', $env->getSiteURL());
        $this->assertEquals('development', $env->getApplicationEnv());
        $this->assertEquals('/usr/local/etc/php', $env->getPhpIniDir());
        $this->assertEquals('/var/www/html', $env->getPwd());
        $this->assertEquals('www-data', $env->getUser());
        $this->assertEquals('/', $env->getRequestUri());
        $this->assertEquals('', $env->getQueryString());
        $this->assertEquals('GET', $env->getRequestMethod());
        $this->assertEquals('/var/www/html/public/index.php', $env->getScriptFilename());
        $this->assertEquals('delboy1978uk@gmail.com', $env->getServerAdmin());
        $this->assertEquals('/var/www/html/public', $env->getDocumentRoot());
        $this->assertEquals('192.168.99.1', $env->getRemoteAddress());
        $this->assertEquals(443, $env->getServerPort());
        $this->assertEquals('awesome.scot', $env->getServerName());
    }

}
