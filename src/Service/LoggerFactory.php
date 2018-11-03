<?php

namespace Bone\Service;

use InvalidArgumentException;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class LoggerFactory
{
    /**
     * @param array $config
     * @return Logger[]
     * @throws \Exception
     */
    public function createLoggers(array $config): array
    {
        $logChannels = [];

        if (!array_key_exists('channels', $config)) {
            throw new InvalidArgumentException('You must have a channels array config');
        }

        foreach ($config['channels'] as $name => $path) {
            $logger = new Logger($name);
            $logger->pushHandler(new StreamHandler($path, Logger::DEBUG));
            $logChannels[$name] = $logger;
        }

        return $logChannels;
    }

}