<?php

namespace Welp\BatchBundle\Manager;

use Welp\BatchBundle\Model\Batch;
use Welp\BatchBundle\Manager\OperationManagerInterface as BaseOperationManager;

/**
 * operation Factory
 */
class OperationManager implements BaseOperationManager
{
    public function __construct()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $operations)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, $errors)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return null;
    }
}
