<?php

use Bone\I18n\Http\Middleware\I18nMiddleware;
use Bone\I18n\Service\TranslatorFactory;
use BoneTest\Http\RequestHandler\I18nTestHandler;
use Codeception\Test\Unit;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Laminas\I18n\Translator\Loader\Gettext;

class I18HandlerTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var I18nMiddleware $middleware */
    private $middleware;

    public function _before()
    {
        $factory = new TranslatorFactory();
        $config = [
            'enabled' => true,
            'translations_dir' => 'tests/_data/translations',
            'type' => Gettext::class,
            'default_locale' => 'en_PI',
            'supported_locales' => ['en_PI', 'en_GB', 'nl_BE', 'fr_BE'],
            'date_format' => 'd/m/Y',
        ];
        $translator = $factory->createTranslator($config);
        $this->middleware = new I18nMiddleware($translator, $config['supported_locales'], $config['default_locale'], $config['enabled']);

    }

    /**
     * @throws \League\Route\Http\Exception\NotFoundException
     */
    public function testLocale()
    {
        $locale = Locale::getDefault();
        $request = new ServerRequest([], [], new Uri('https://awesome.scot/en_PI/somepage'));
        $handler = new I18nTestHandler();
        $response = $this->middleware->process($request, $handler);
        $this->assertEquals('locale is en_PI and path is /somepage', $response->getBody()->getContents());
        $newLocale = Locale::getDefault();
        $this->assertTrue($locale !== $newLocale);
        $this->assertEquals('en_PI', $newLocale);
    }

    /**
     * @throws \League\Route\Http\Exception\NotFoundException
     */
    public function testAnotherLocale()
    {
        $locale = Locale::getDefault();
        $request = new ServerRequest([], [], new Uri('https://awesome.scot/en_GB/somepage'));
        $handler = new I18nTestHandler();
        $response = $this->middleware->process($request, $handler);
        $this->assertEquals('locale is en_GB and path is /somepage', $response->getBody()->getContents());
        $newLocale = Locale::getDefault();
        $this->assertTrue($locale !== $newLocale);
        $this->assertEquals('en_GB', $newLocale);
    }

    /**
     * @throws \League\Route\Http\Exception\NotFoundException
     */
    public function testUnsupportedLocale()
    {
        $request = new ServerRequest([], [], new Uri('https://awesome.scot/es_ES/somepage'));
        $handler = new I18nTestHandler();
        $response = $this->middleware->process($request, $handler);
        $this->assertEquals('locale is en_PI and path is /somepage', $response->getBody()->getContents());
        $newLocale = Locale::getDefault();
        $this->assertTrue('en_PI' === $newLocale);
    }

    /**
     * @throws \League\Route\Http\Exception\NotFoundException
     */
    public function testPathWithoutLocale()
    {
        $request = new ServerRequest([], [], new Uri('https://awesome.scot/somepage'));
        $handler = new I18nTestHandler();
        $response = $this->middleware->process($request, $handler);
        $this->assertEquals('locale is en_PI and path is /somepage', $response->getBody()->getContents());
        $newLocale = Locale::getDefault();
        $this->assertEquals('en_PI', $newLocale);
    }
}
