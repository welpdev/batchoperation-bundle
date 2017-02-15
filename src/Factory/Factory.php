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
        //check if operations is well formatted ( it must have a type in each "row")
        foreach ($operations as $operation) {
            if (! array_key_exists('type', $operation)) {
                throw new \Exception('all rows must have a "type" key');
            }
        }

        $batch = new Batch();
        $batch->setStatus(Batch::STATUS_PENDING);
        $batch->setOperations($operations);

        $this->entityManager->persist($batch);
        $this->entityManager->flush();

        //produce the operations to rmq
        $this->container->get('welp_batch.producer')->produceToRmq($operations, $batch->getId());

        return $batch;
    }

    public function getBatch($id)
    {
        $batch = $this->batchRepository->findOneById($id);

        return $batch;
    }

    public function updateBatchStatus($id)
    {
        $batch = $this->batchRepository->findOneById($id);

        //TODO active or finished
        $batch->setStatus(Batch::STATUS_ACTIVE);

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
