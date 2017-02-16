<?php

namespace Welp\BatchBundle\Producer;

/**
 *
 */
class RabbitMQProducer implements ProducerInterface
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function produce($operation, $batchId, $type, $action)
    {
        $serviceName = $this->selectQueue($type, $action);

        $operation['batchId']=$batchId;
        $sMsg = serialize($operation);
        $this->container->get($serviceName)->publish($sMsg);
    }

    private function selectQueue($entity, $action)
    {
        $serviceName = sprintf('old_sound_rabbit_mq.%s_producer', 'welp_batch_'.$entity);
        return $serviceName;
    }
}
