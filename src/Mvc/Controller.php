<?php

namespace Bone\Mvc;

use Bone\Mvc\Controller\LocaleAwareInterface;
use Bone\Mvc\Controller\Traits\HasTranslatorTrait;
use Bone\Server\SiteConfig;

class Controller implements LocaleAwareInterface
{
    use HasTranslatorTrait;
}
