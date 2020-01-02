<?php

use Bone\Mvc\View\Extension\Plates\LocaleLink;
use Bone\Service\TranslatorFactory;
use Codeception\TestCase\Test;
use Zend\I18n\Translator\Loader\Gettext;
use Zend\I18n\Translator\Translator;

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