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
        $config = $this->globLoadConfig($configFolder);

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
        $config = $this->globLoadConfig($path);

        return $config;
    }

    private function globLoadConfig($path)
    {
        if (file_exists($path)) {
            $files = glob($path . '/*.php');
            foreach ($files as $file) {
                $config = $this->loadInConfig($config, $file);
            }
        }
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

    /**
     * @return string
     */
    public function getApplicationEnv(): string
    {
        return $this->getAttribute('APPLICATION_ENV');
    }

    /**
     * @return string
     */
    public function getPhpIniDir(): string
    {
        return $this->getAttribute('PHP_INI_DIR');
    }

    /**
     * @return string
     */
    public function getPwd(): string
    {
        return $this->getAttribute('PWD');
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->getAttribute('USER');
    }

    /**
     * @return string
     */
    public function getRequestUri(): string
    {
        return $this->getAttribute('REQUEST_URI');
    }

    /**
     * @return string
     */
    public function getQueryString(): string
    {
        return $this->getAttribute('QUERY_STRING');
    }

    /**
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->getAttribute('REQUEST_METHOD');
    }

    /**
     * @return string
     */
    public function getScriptFilename(): string
    {
        return $this->getAttribute('SCRIPT_FILENAME');
    }

    /**
     * @return string
     */
    public function getServerAdmin(): string
    {
        return $this->getAttribute('SERVER_ADMIN');
    }

    /**
     * @return string
     */
    public function getRequestScheme(): string
    {
        return $this->getAttribute('REQUEST_SCHEME');
    }

    /**
     * @return string
     */
    public function getDocumentRoot(): string
    {
        return $this->getAttribute('DOCUMENT_ROOT');
    }

    /**
     * @return string
     */
    public function getRemoteAddress(): string
    {
        return $this->getAttribute('REMOTE_ADDR');
    }

    /**
     * @return string
     */
    public function getServerPort(): string
    {
        return $this->getAttribute('SERVER_PORT');
    }

    /**
     * @return string
     */
    public function getServerName(): string
    {
        return $this->getAttribute('SERVER_NAME');
    }

    /**
     * @return string
     */
    public function getHttpHost(): string
    {
        return $this->getAttribute('HTTP_HOST');
    }

    /**
     * @return string
     */
    public function getSiteURL() : string
    {
        return $this->getRequestScheme() . '://' . $this->getHttpHost();
    }
}