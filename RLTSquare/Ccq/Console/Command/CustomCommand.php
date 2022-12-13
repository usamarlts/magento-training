<?php

declare(strict_types=1);

namespace RLTSquare\Ccq\Console\Command;

use Exception;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CustomCommand extends Command
{
    private const INPUT_OPTION_VAR1 = 'variable1';
    private const INPUT_OPTION_VAR2 = 'variable2';

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
     * @var InputOption
     */
    protected InputOption $inputOption;

    /**
     * @param PublisherInterface $publisher
     * @param LoggerInterface $logger
     * @param Json $json
     * @param InputOption $inputOption
     */
    public function __construct(
        PublisherInterface $publisher,
        LoggerInterface $logger,
        Json $json,
        InputOption $inputOption
    ) {
        $this->publisher = $publisher;
        $this->logger = $logger;
        $this->json = $json;
        $this->inputOption = $inputOption;
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setName('rltsquare:hello:world')->setDescription('Custom Command');
        $this->addOption(self::INPUT_OPTION_VAR1, "-var1", InputOption::VALUE_REQUIRED, "Variable1");
        $this->addOption(self::INPUT_OPTION_VAR2, "-var2", InputOption::VALUE_REQUIRED, "Variable2");
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
        $var1 = $input->getArgument(self::INPUT_OPTION_VAR1);
        $var2 = $input->getArgument(self::INPUT_OPTION_VAR2);

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
