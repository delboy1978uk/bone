<?php declare(strict_types=1);

namespace Bone\Traits;

use Psr\Log\LoggerInterface;

trait HasLoggerTrait
{
    /** @var LoggerInterface $logger */
    private $logger;

    /**
     * @return LoggerInterface
     */
    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}