<?php

namespace Bone\Server;

interface SiteConfigAwareInterface
{
    /**
     * @param SiteConfig $config
     */
    public function setSiteConfig(SiteConfig $config): void;

    /**
     * @return SiteConfig
     */
    public function getSiteConfig(): SiteConfig;
}
