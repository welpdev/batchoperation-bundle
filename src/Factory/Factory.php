<?php

namespace Welp\BatchBundle\Factory;

use Welp\BatchBundle\Entity\Batch;

/**
 * batch Factory
 */
class Factory
{
    private $entityManager;
    private $batchRepository;
    private $container;
    private $rmqProducer;

    public function __construct($entityManager, $container)
    {
        $this->entityManager = $entityManager;
        $this->batchRepository = $this->entityManager->getRepository(Batch::class);
        $this->container = $container;
    }

    public function createBatch(array $operations)
    {
        $batch = new Batch();
        $batch->setStatus(Batch::STATUS_PENDING);
        $batch->setOperations($operations);

        $this->entityManager->persist($batch);
        $this->entityManager->flush();

        //produce the operations to rmq TODO add batchid to let consumer update batch status
        $this->container->get('welp_batch.producer')->produceToRmq($operations);

        return $batch;
    }

    public function getBatch($id)
    {
        $batch = $this->batchRepository->findOneById($id);

        return $batch;
    }

    public function updateBatchStatus($status)
    {
        $batch->setStatus($status);

        $this->entityManager->persist($batch);
        $this->entityManager->flush();
    }

    public function deleteBatch($id)
    {
        $batch = $this->batchRepository->findOneById($id);
        $this->entityManager->remove($batch);
        $this->entityManager->flush();
    }
}
