<?php

namespace Bone\Mvc;

use Bone\Mvc\Controller\LocaleAwareInterface;
use Bone\Mvc\Controller\Traits\HasTranslatorTrait;
use Bone\Mvc\Controller\Traits\HasViewTrait;
use Bone\Mvc\Controller\ViewAwareInterface;
use Bone\Server\SiteConfig;

class Controller implements LocaleAwareInterface, ViewAwareInterface
{
    use HasTranslatorTrait;
    use HasViewTrait;
}
