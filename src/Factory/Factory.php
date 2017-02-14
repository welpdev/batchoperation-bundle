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

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
        $this->batchRepository = $this->entityManager->getRepository('Batch') = $batchRepository;
    }

    public function createBatch(array $operations)
    {
        $batch = new Batch();
        $batch->setStatus(Batch::STATUS_PENDING);
        $batch->setOperations($operations);

        $this->entityManager->persist($batch);
        $this->entityManager->flush();

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
