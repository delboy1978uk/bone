<?php

namespace Bone\I18n;

interface I18nRegistrationInterface
{
    /**
     * @return string
     */
    public function getTranslationsDirectory(): string;
}