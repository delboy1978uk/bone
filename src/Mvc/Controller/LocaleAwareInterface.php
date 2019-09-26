<?php

namespace Bone\Mvc\Controller;

use Zend\I18n\Translator\Translator;

interface LocaleAwareInterface
{
    public function setTranslator(Translator $translator): void ;
    public function getTranslator(): Translator;
}
