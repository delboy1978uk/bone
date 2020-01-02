<?php

use Bone\Server\Environment;
use Bone\Server\SiteConfig;
use Bone\Service\TranslatorFactory;
use Codeception\TestCase\Test;
use Zend\Diactoros\ServerRequest;
use Zend\Diactoros\Uri;
use Zend\I18n\Translator\Loader\Gettext;

class SiteConfigTest extends Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /**
     * @throws \League\Route\Http\Exception\NotFoundException
     */
    public function testLocale()
    {
        $config = [
            'site' => [
                'title' => 'Bone MVC Framework',
                'domain' => 'awesome.scot',
                'baseUrl' => 'https://awesome.scot',
                'contactEmail' => 'abc@awesome.scot',
                'serverEmail' => 'noreply@awesome.scot',
                'company' => 'Pirates Inc.',
                'address' => '1 Big Tree, Booty Island',
                'logo' => '/img/skull_and_crossbones.png',
                'emailLogo' => '/img/emails/logo.png',
            ],
        ];
        $env = new Environment($config);
        $siteConfig = new SiteConfig($config, $env);

        foreach ($config['site'] as $key => $value) {
            $method = 'get' . ucfirst($key);
            $this->assertEquals($value, $siteConfig->$method());
        }

        $this->assertInstanceOf(Environment::class, $siteConfig->getEnvironment());
    }
}