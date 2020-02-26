<?php

use Bone\I18n\View\Extension\LocaleLink;
use Bone\I18n\Service\TranslatorFactory;
use Codeception\TestCase\Test;
use Laminas\I18n\Translator\Loader\Gettext;
use Laminas\I18n\Translator\Translator;

class LocaleLinkTest extends Test
{
    /**
     * @var \UnitTester
     */
    protected $tester;


    public function testLink()
    {
        $locale = Locale::getDefault();
        $viewHelper = new LocaleLink();
        $this->assertEquals('/' . $locale, $viewHelper->locale());
    }
}