<?php

namespace Welp\BatchBundle\Factory;

use Welp\BatchBundle\Entity\Batch;

/**
 *
 */
class OperationProducerService
{
    private $container;
    private $rmqProducer;

    public function __construct($container, $rmqProducerName)
    {
        $this->container = $container;
        $this->rmqProducer= $this->container->get($rmqProducerName);
    }

    public function produceToRmq($operations)
    {
        foreach ($operations as $operation) {
            $sMsg = serialize($operation);
            dump($this->container->getParameter('welp_batch.rabbitmq_producer_routing_keys'));
            $this->rmqProducer->publish($sMsg, $this->container->getParameter('welp_batch.rabbitmq_producer_routing_keys'));
        }
    }
}
