<?php

declare(strict_types=1);

namespace RLTSquare\Ccq\Model\Consumer;

use Exception;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;

/**
 * Consumer class for rltsquare_hello_world job
 */
class Consumer
{
    /**
     * @var Json
     */
    protected Json $json;
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param LoggerInterface $logger
     * @param Json $json
     */
    public function __construct(
        LoggerInterface $logger,
        Json $json
    ) {
        $this->logger = $logger;
        $this->json = $json;
    }

    /**
     * this method process the queue message
     * @param $request
     * @return void
     */
    public function process($request): void
    {
        try {
            $this->logger->info('hello world from rltsquare.hello.world.queue job');
            $arr=$this->json->unserialize($request);
            foreach ($arr as $key=>$value) {
                $this->logger->info("$key=>$value");
            }
        } catch (Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
