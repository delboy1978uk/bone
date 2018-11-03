<?php

namespace Bone\Mvc\View\Extension\Plates;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Zend\I18n\Translator\Translator;

class Translate implements ExtensionInterface
{
    /** @var Translator $translator */
    private $translator;

    /**
     * Translate constructor.
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param Engine $engine
     */
    public function register(Engine $engine)
    {
        $engine->registerFunction('t', [$this, 'translate']);
        $engine->registerFunction('translate', [$this, 'translate']);
    }

    /**
     * @param string $string
     * @return string
     */
    public function translate(string $string) : string
    {
        return $this->translator->translate($string);
    }
}
