<?php

namespace Welp\BatchBundle\Manager;

use Welp\BatchBundle\Model\Batch;
use Welp\BatchBundle\Manager\ManagerInterface as BaseManager;

/**
 * batch Factory
 */
class BatchManager implements BaseManager
{
    private $entityManager;
    private $container;
    private $repository;
    private $class;

    public function __construct($entityManager, $container, $className)
    {
        $this->container = $container;
        $this->entityManager = $this->container->get($entityManager);
        $this->repository = $this->entityManager->getRepository($className);
        //$metadata = $this->entityManager->getClassMetadata($className);
        $this->class = $className;
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
        //handle timestamp
        $entity->setCreatedAt(new \DateTime());
        $entity->setUpdatedAt(new \DateTime());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $batch = $this->entityManager->findOneById($id);

        return $batch;
    }

    /**
     * {@inheritdoc}
     */
    public function update($entity)
    {
        $entity->setUpdatedAt(new \DateTime());

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
