<?php

namespace Welp\BatchBundle\Manager;

use Welp\BatchBundle\Model\Operation;

/**
 * batch Factory
 */
interface BatchManagerInterface
{
    /**
     * Create a new operation with the given operations
     * @param  array  $operations
     * @return Operation
     */
    public function create(array $operations);

    /**
     * Get an operation with the given id
     * @param  int $id
     * @return Operation
     */
    public function get($id);

    /**
     * Update an operation with the given ID
     * @param  int $id
     * @param  array $errors
     * @return Batch
     */
    public function update($id, $errors);

    /**
     * Delete an operation with the given ID
     * @param  int $id
     * @return boolean
     */
    public function delete($id);
}
