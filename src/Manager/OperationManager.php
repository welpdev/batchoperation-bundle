<?php

namespace Welp\BatchBundle\Manager;

use Welp\BatchBundle\Model\Batch;
use Welp\BatchBundle\Manager\ManagerInterface as BaseManager;

/**
 * operation Factory
 */
class OperationManager implements BaseManager
{
    /**
     * @var String
     */
    private $class;


    /**
     *
     * @param String $entityManager name of the entitytyManager service
     * @param ContainerInterface $container
     * @param String $className     Name of the class that extends our batchModel
     */
    public function __construct()
    {
        $this->class = "Welp\BatchBundle\Model\Operation";
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $operation = new $this->class();
        return $operation;
    }

    /**
     * {@inheritdoc}
     */
    public function create($entity)
    {
        $entity->setCreatedAt(new \DateTime());
        $entity->setUpdatedAt(new \DateTime());

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function update($entity)
    {
        $entity->setUpdatedAt(new \DateTime());
        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($entity)
    {
        return false;
    }
}
