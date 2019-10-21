<?php

namespace Bone\Service;

use Bone\I18n\I18nRegistrationInterface;
use Locale;
use Zend\I18n\Translator\Loader\Gettext;
use Zend\I18n\Translator\Translator;

class TranslatorFactory
{
    /**
     * @param array $config
     * @return Translator
     */
    public function createTranslator(array $config, $domain = 'default')
    {
        $translator = new Translator();

        foreach ($config['supported_locales'] as $locale) {
            $file = $config['translations_dir'] . '/' . $locale .  '/' . $locale . '.mo';
            $translator->addTranslationFile(
                $config['type'],
                $file,
                $domain,
                $locale
            );
        }
        $translator->setLocale(Locale::getDefault());

        return $translator;
    }

    /**
     * @param Translator $translator
     * @param I18nRegistrationInterface $package
     * @param string $locale
     * @return Translator
     */
    public function addPackageTranslations(Translator $translator, I18nRegistrationInterface $package, string $locale)
    {
        $dir = $package->getTranslationsDirectory();
        $translator->addTranslationFile(
            Gettext::class,
            $dir . '/' . $locale . '/' . $locale . '.mo',
            'user',
            $locale
        );

        return $translator;
    }
}