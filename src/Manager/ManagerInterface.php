<?php

namespace Welp\BatchBundle\Manager;

use Welp\BatchBundle\Model\Batch;

/**
 * batch Factory
 */
interface ManagerInterface
{
    /**
     * Create a new entity
     * @param  array  $operations
     * @return Batch
     */
    public function createNew();

    /**
     * save a new entity
     * @param  array  $operations
     * @return Batch
     */
    public function create($entity);

    /**
     * get an entity
     * @param  int $id
     * @return Batch
     */
    public function get($entity);

    /**
     * update an entity
     * @param  int $id
     * @return Batch
     */
    public function update($id);

    /**
     * Delete a batch with the given ID
     * @param  int $id
     * @return bool
     */
    public function delete($id);
}
