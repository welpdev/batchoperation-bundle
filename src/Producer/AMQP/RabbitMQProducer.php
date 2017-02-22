<?php

namespace Welp\BatchBundle\Producer\AMQP;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Welp\BatchBundle\Producer\ProducerInterface as BaseProducer;

/**
 * Rabbitmq producer
 */
class RabbitMQProducer implements BaseProducer
{
    /**
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     *
     * @param ContainerInterface $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function produce($operation, $batchId, $type, $action)
    {
        $serviceName = $this->selectQueue($type, $action);

        $message = array();
        $message['batchId']=$batchId;
        $message['operationId'] = $operation->getId();
        $message['operationPayload']=$operation->getPayload();
        $message['type']=$type;
        $message['action']=$action;

        $sMsg = serialize($message);
        $this->container->get($serviceName)->setupFabric();
        $this->container->get($serviceName)->publish($sMsg, 'welp.batch.'.$type.'.'.$action);
    }

    /**
     * {@inheritdoc}
     */
    public function selectQueue($entity, $action)
    {
        $serviceName = sprintf('old_sound_rabbit_mq.%s_producer', 'welp_batch_'.$entity.'_'.$action);
        return $serviceName;
    }
}
