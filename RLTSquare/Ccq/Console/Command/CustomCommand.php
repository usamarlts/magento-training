<?php

declare(strict_types=1);

namespace RLTSquare\Ccq\Console\Command;

use Exception;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CustomCommand extends Command
{
    private const VARIABLE1 = '-var1';
    private const VARIABLE2 = '-var2';

    /**
     * @var Json
     */
    protected Json $json;
    /**
     * @var PublisherInterface
     */
    protected PublisherInterface $publisher;
    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

    /**
     * @param PublisherInterface $publisher
     * @param LoggerInterface $logger
     * @param Json $json
     */
    public function __construct(
        PublisherInterface $publisher,
        LoggerInterface $logger,
        Json $json
    ) {
        $this->publisher = $publisher;
        $this->logger = $logger;
        $this->json = $json;
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('rltsquare:hello:world')->setDescription('Custom Command');
        $this->setDefinition([
            new InputArgument(self::VARIABLE1, InputArgument::REQUIRED, "Variable1"),
            new InputArgument(self::VARIABLE2, InputArgument::REQUIRED, "Variable1")
        ]);
        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $var1 = $input->getArgument(self::VARIABLE1);
        $var2 = $input->getArgument(self::VARIABLE2);

        try {
            if (!empty($var1) && !empty($var2)) {
                $arr = $this->json->serialize(["var1" => $var1, "var2" => $var2]);
                $this->publisher->publish('rltsquare.hello.world.queue.topic', $arr);
                $output->writeln("$var1 $var2 added to rltsquare_hello_world_queue");
            }
        } catch (Exception $e) {
            $output->writeln(
                sprintf(
                    '<error>%s</error>',
                    $e->getMessage()
                )
            );
        }
        return 0;
    }
}
