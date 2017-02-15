<?php

namespace Welp\BatchBundle\Manager;

use Welp\BatchBundle\Model\Batch;

/**
 * batch Factory
 */
interface BatchManagerInterface
{
    /**
     * Create a new batch with the given operations
     * @param  array  $operations
     * @return Batch
     */
    public function create(array $operations);

    /**
     * Get a batch with the given id
     * @param  int $id
     * @return Batch
     */
    public function get($id);

    /**
     * Update a batch with tthe given ID
     * @param  int $id
     * @param  array $errors
     * @return Batch
     */
    public function update($id, $errors);

    /**
     * Delete a batch with the given ID
     * @param  int $id
     * @return boolean
     */
    public function delete($id);
}
