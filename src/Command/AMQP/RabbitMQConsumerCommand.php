<?php

namespace Welp\BatchBundle\Command\AMQP;

use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 * rabbitmq-cli-consumer -e "/var/www/welp/current/app/console api:consumer:batch:operation" -c app/config/rabbitmq-cli.conf -V
 *
 */
class RabbitMQConsumerCommand extends ContainerAwareCommand
{
    private $serviceName;

    private $commandName;

    public function __construct($serviceName, $commandName)
    {
        $this->serviceName = $serviceName;
        $this->commandName = $commandName;
    }

    protected function configure()
    {
        $this
            ->addArgument('event', InputArgument::REQUIRED)
            ->setName($this->commandName)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = base64_decode($input->getArgument('event'));
        $message = new AMQPMessage($data);

        /** @var \PhpAmqpLib\Message\AMQPMessage\ConsumerInterface $consumer */
        $consumer = $this->getContainer()->get($this->serviceName);

        if (false == $consumer->execute($message)) {
            exit(1);
        }

        exit(0);
    }
}
