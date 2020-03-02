<?php

namespace Bone\Controller;

use Bone\I18n\I18nAwareInterface;
use Bone\Traits\HasSiteConfigTrait;
use Bone\Traits\HasTranslatorTrait;
use Bone\Traits\HasViewTrait;
use Bone\View\ViewAwareInterface;
use Bone\Server\SiteConfigAwareInterface;

class Controller implements I18nAwareInterface, ViewAwareInterface, SiteConfigAwareInterface
{
    use HasSiteConfigTrait;
    use HasTranslatorTrait;
    use HasViewTrait;
}
