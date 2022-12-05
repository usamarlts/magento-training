<?php

declare(strict_types=1);

namespace RLTSquare\Ccq\Cron;

use Exception;
use Psr\Log\LoggerInterface;

class CustomCron
{
    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Write to system.log
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->logger->info('hello world from rltsquare_hello_world cron job');
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
