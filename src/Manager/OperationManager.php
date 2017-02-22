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
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EntityRepository
     */
    private $repository;

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
    public function __construct($entityManager, $container, $className)
    {
        $this->container = $container;
        //$this->entityManager = $this->container->get($entityManager);
        //$this->repository = $this->entityManager->getRepository($className);

        //$metadata = $this->entityManager->getClassMetadata($className);
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
        //handle timestamp
        $entity->setCreatedAt(new \DateTime());
        $entity->setUpdatedAt(new \DateTime());

    /*    $this->entityManager->persist($entity);
        $this->entityManager->flush();*/

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        //$entity = $this->repository->findOneById($id);
        //return $entity;
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function update($entity)
    {
        $entity->setUpdatedAt(new \DateTime());
        return $entity;
        /*$this->entityManager->    persist($entity);
        $this->entityManager->flush();*/
    }

    /**
     * {@inheritdoc}
     */
    public function delete($entity)
    {
        return false;
        /*$this->entityManager->remove($entity);
        $this->entityManager->flush();*/
    }
}
