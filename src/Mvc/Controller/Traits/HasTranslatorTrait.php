<?php

namespace Bone\Mvc\Controller\Traits;

use Zend\I18n\Translator\Translator;

trait HasTranslatorTrait 
{
    /** @var Translator $translator */
    private $translator;

    public function setTranslator(Translator $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * @return Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }
}