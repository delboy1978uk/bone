<?php

namespace Bone\Server;

use Bone\Traits\HasAttributesTrait;

class Environment
{
    use HasAttributesTrait;

    /**
     * Environment constructor.
     * @param array $serverGlobals
     */
    public function __construct(array $serverGlobals)
    {
        $this->setAttributes($serverGlobals);
    }

    /**
     * @param string $configFolder
     * @param string $applicationEnvironment
     * @return array
     */
    public function fetchConfig(string $configFolder, string $applicationEnvironment) : array
    {
        $config = $this->loadLegacyConfig($configFolder);

        if (!empty($applicationEnvironment)) {
            $config = $this->loadEnvironmentConfig($configFolder, $applicationEnvironment, $config);
        }

        return $config;
    }

    /**
     * @param string $configFolder
     * @return array
     */
    private function loadLegacyConfig(string $configFolder): array
    {
        $config = [];

        $path = $configFolder . '/config.php';
        if (file_exists($path)) {
            $config = $this->loadInConfig($config, $path);
        }

        return $config;
    }

    /**
     * @param string $configFolder
     * @param string $applicationEnvironment
     * @param array $config
     * @return array
     */
    private function loadEnvironmentConfig(string $configFolder, string $applicationEnvironment, array $config): array
    {
        $path = $configFolder . '/' . $applicationEnvironment;
        if (file_exists($path)) {
            $files = glob($path . '/*.php');
            foreach ($files as $file) {
                $config = $this->loadInConfig($config, $file);
            }
        }

        return $config;
    }

    /**
     * @param array $config
     * @param string $file
     * @return array
     */
    private function loadInConfig(array $config, string $file): array
    {
        $moreConfig = include $file;
        if (is_array($moreConfig)) {
            $config = array_merge($config, $moreConfig);
        }

        return $config;
    }
}