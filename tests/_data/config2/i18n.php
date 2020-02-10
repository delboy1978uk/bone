<?php

use Laminas\I18n\Translator\Loader\Gettext;

return [
    'i18n' => [
        'enabled' => false,
        'translations_dir' => 'tests/_data/translations',
        'type' => Gettext::class,
        'default_locale' => 'en_GB',
        'supported_locales' => ['en_GB', 'nl_BE', 'fr_BE'],
    ],
];