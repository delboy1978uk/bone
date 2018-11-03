<?php

namespace Bone\Service;

use Zend\I18n\Translator\Translator;

class TranslatorFactory
{
    /**
     * @param array $config
     * @return Translator
     */
    public function createTranslator(array $config)
    {
        $translator = new Translator();

        foreach ($config['supported_locales'] as $locale) {
            $translator->addTranslationFilePattern($config['type'], $config['translations_dir'], '%1$s/' . $locale . '.mo');
        }
        $translator->setLocale($config['default_locale']);
        return $translator;
    }
}