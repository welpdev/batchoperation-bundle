<?php

namespace Welp\BatchBundle\Producer;

/**
 * Producer interface
 */
interface ProducerInterface
{
    /**
     * publish an operation to the broker
     * @param  array  $operations
     */
    public function produce($operations, $batchId, $type, $action);

    /**
     * Select th right queue to use
     * @param  string $entity Class name of the entity
     * @param  string $action Action to do ( create, delete)
     * @return string         Service to use
     */
    public function selectQueue($entity, $action);
}
