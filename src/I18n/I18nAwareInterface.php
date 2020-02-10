<?php

namespace Bone\I18n;

use Laminas\I18n\Translator\Translator;

interface I18nAwareInterface
{
    /**
     * @return Translator
     */
    public function getTranslator(): Translator;

    /**
     * @param Translator $translator
     */
    public function setTranslator(Translator $translator): void;
}
