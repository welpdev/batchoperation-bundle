<?php

namespace Welp\BatchBundle\Producer\AMQP;

use Welp\BatchBundle\Producer\ProducerInterface as BaseProducer;

/**
 *
 */
class RabbitMQProducer implements BaseProducer
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function produce(array $operation, $batchId, $type, $action)
    {
        $serviceName = $this->selectQueue($type, $action);

        $operation['batchId']=$batchId;
        $sMsg = serialize($operation);
        $this->container->get($serviceName)->setupFabric();
        $this->container->get($serviceName)->publish($sMsg, 'welp.batch.'.$type.'.'.$action);
    }

    public function selectQueue($entity, $action)
    {
        $serviceName = sprintf('old_sound_rabbit_mq.%s_producer', 'welp_batch_'.$entity.'_'.$action);
        return $serviceName;
    }
}
