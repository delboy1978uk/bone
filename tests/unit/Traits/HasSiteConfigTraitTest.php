<?php

use Bone\Server\Environment;
use Bone\Server\SiteConfig;
use Bone\Server\Traits\HasSiteConfigTrait;
use Codeception\TestCase\Test;

class HasSiteConfigTraitTest extends Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testGetSet()
    {
        $class = new class {
            use HasSiteConfigTrait;
        };

        $data =  [
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
        $env = new Environment($data);
        $config = new SiteConfig($data, $env);
        $class->setSiteConfig($config);
        $this->assertInstanceOf(SiteConfig::class, $class->getSiteConfig());
    }
}