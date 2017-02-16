<?php

namespace Welp\BatchBundle\Adapter;

/**
 * batch Factory
 */
interface AdpaterInterface
{
    /**
     * publish an operation to the broker
     * @param  array  $operations
     */
    public function produce(array $operations);

    /**
     * Select th right queue to use
     * @param  string $entity Class name of the entity
     * @param  string $action Action to do ( create, delete)
     * @return string         Service to use
     */
    public function selectQueue($entity, $action);
}
