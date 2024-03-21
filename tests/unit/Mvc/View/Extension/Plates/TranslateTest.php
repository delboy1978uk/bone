<?php

use Bone\I18n\View\Extension\Translate;
use Bone\I18n\Service\TranslatorFactory;
use Codeception\Test\Unit;
use Laminas\I18n\Translator\Loader\Gettext;
use Laminas\I18n\Translator\Translator;

class TranslateTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    /** @var Translator */
    private $translator;

    public function _before()
    {
        $factory = new TranslatorFactory();
        $config = [
            'enabled' => false,
            'translations_dir' => 'tests/_data/translations',
            'type' => Gettext::class,
            'default_locale' => 'en_GB',
            'supported_locales' => ['en_PI', 'en_GB', 'nl_BE', 'fr_BE'],
            'date_format' => 'd/m/Y',
        ];
        Locale::setDefault($config['default_locale']);
        $translator = $factory->createTranslator($config);
        $this->translator = $translator;
    }


    public function testTranslate()
    {
        $translate = new Translate($this->translator);
        $greeting = $translate->translate('greeting');
        $this->assertEquals('Hello', $greeting);
        Locale::setDefault('nl_BE');
        $greeting = $translate->translate('greeting');
        $this->assertEquals('Hoi', $greeting);
    }
}
