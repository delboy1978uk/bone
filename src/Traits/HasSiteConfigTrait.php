<?php

namespace Bone\Traits;

use Bone\Server\SiteConfig;

trait HasSiteConfigTrait
{
    /** @var SiteConfig $siteConfig */
    private $siteConfig;

    /**
     * @return SiteConfig
     */
    public function getSiteConfig(): SiteConfig
    {
        return $this->siteConfig;
    }

    /**
     * @param SiteConfig $siteConfig
     */
    public function setSiteConfig(SiteConfig $siteConfig): void
    {
        $this->siteConfig = $siteConfig;
    }
}
