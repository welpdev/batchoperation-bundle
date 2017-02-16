<?php

namespace Welp\BatchBundle\Manager;

use Welp\BatchBundle\Model\Batch;
use Welp\BatchBundle\Manager\ManagerInterface as BaseManager;

/**
 * operation Factory
 */
class OperationManager implements BaseManager
{
    private $entityManager;
    private $container;
    private $repository;
    private $class;

    public function __construct($entityManager, $container, $className)
    {
        $this->entityManager = $entityManager;
        $this->container = $container;
        $this->repository = $this->container->getRepository($className);

        $metadata = $entityManager->getClassMetadata($className);
        $this->class = $metadata->getName();
    }

    public function createNew()
    {
        $batch = new $this->class();
        return $batch;
    }

    /**
     * {@inheritdoc}
     */
    public function create($entity)
    {
        $this->entityManager->persist($batch);
        $this->entityManager->flush();

        return $batch;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $batch = $this->batchRepository->findOneById($id);

        return $batch;
    }

    /**
     * {@inheritdoc}
     */
    public function update($entity)
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
