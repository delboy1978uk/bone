<?php

use Bone\I18n\Http\Middleware\I18nHandler;
use Bone\I18n\Service\TranslatorFactory;
use Codeception\TestCase\Test;
use Laminas\Diactoros\ServerRequest;
use Laminas\Diactoros\Uri;
use Laminas\I18n\Translator\Loader\Gettext;

class I18HandlerTest extends Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var I18nHandler $middleware */
    private $middleware;

    private $translator;

    public function _before()
    {
        $factory = new TranslatorFactory();
        $config = [
            'enabled' => false,
            'translations_dir' => 'tests/_data/translations',
            'type' => Gettext::class,
            'default_locale' => 'en_PI',
            'supported_locales' => ['en_PI', 'en_GB', 'nl_BE', 'fr_BE'],
            'date_format' => 'd/m/Y',
        ];
        $this->translator = $factory->createTranslator($config);
        $this->middleware = new I18nHandler($this->translator, $config['supported_locales'], $config['default_locale']);

    }

    /**
     * @throws \League\Route\Http\Exception\NotFoundException
     */
    public function testLocale()
    {
        $locale = Locale::getDefault();
        $request = new ServerRequest([], [], new Uri('https://awesome.scot/en_PI/somepage'));
        $request = $this->middleware->handleI18n($request);
        $this->assertEquals('/somepage', $request->getUri()->getPath());
        $newLocale = Locale::getDefault();
        $this->assertTrue($locale !== $newLocale);
        $this->assertEquals('en_PI', $newLocale);
    }

    /**
     * @throws \League\Route\Http\Exception\NotFoundException
     */
    public function testUnsupportedLocale()
    {
        $locale = Locale::getDefault();
        $request = new ServerRequest([], [], new Uri('https://awesome.scot/es_ES/somepage'));
        $request = $this->middleware->handleI18n($request);
        $this->assertEquals('/es_ES/somepage', $request->getUri()->getPath());
        $newLocale = Locale::getDefault();
        $this->assertTrue($locale === $newLocale);
    }

    /**
     * @throws \League\Route\Http\Exception\NotFoundException
     */
    public function testRemoveLocale()
    {
        $locale = Locale::getDefault();
        $request = new ServerRequest([], [], new Uri('https://awesome.scot/es_ES/somepage'));
        $request = $this->middleware->removeI18n($request);
        $this->assertEquals('/somepage', $request->getUri()->getPath());
        $newLocale = Locale::getDefault();
        $this->assertTrue($locale === $newLocale);
    }

    /**
     * @throws \League\Route\Http\Exception\NotFoundException
     */
    public function testPathWithoutLocale()
    {
        $locale = Locale::getDefault();
        $request = new ServerRequest([], [], new Uri('https://awesome.scot/somepage'));
        $request = $this->middleware->handleI18n($request);
        $this->assertEquals('/somepage', $request->getUri()->getPath());
        $newLocale = Locale::getDefault();
        $this->assertTrue($locale === $newLocale);
    }
}