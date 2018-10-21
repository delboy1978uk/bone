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
        $config = [];

        // load the config.php if it exists
        $path = $configFolder . '/config.php';
        if (file_exists($path)) {
            $config = require_once $path;
        }

        // check environment config folder exists
        $path = $configFolder . '/' . $applicationEnvironment;
        if (file_exists($path)) {
            $files = glob($path . '/*.php');
            foreach ($files as $file) {
                $config = array_merge($config, require_once $file);
            }
        }

        return $config;
    }
}