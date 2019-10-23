<?php

namespace Bone\Mvc\View\Extension\Plates;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;
use Locale;

class LocaleLink implements ExtensionInterface
{
    /**
     * @param Engine $engine
     */
    public function register(Engine $engine)
    {
        $engine->registerFunction('l', [$this, 'locale']);
        $engine->registerFunction('locale', [$this, 'locale']);
    }

    /**
     * @return string
     */
    public function locale() : string
    {
        return '/' . Locale::getDefault();
    }
}
