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
        $batch->setTotalOperations(count($operations));
        $batch->setTotalExecutedOperations(0);

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

    public function updateBatch($id, $errors)
    {
        $batch = $this->batchRepository->findOneById($id);

        $totalOperations = $batch->getTotalOperations();
        $totalExecutedOperations = $batch->getTotalExecutedOperations();

        $totalExecutedOperations+=1;
        $batch->setTotalExecutedOperations($totalExecutedOperations);
        if (count($errors)>0) {
            $errors['id_operation']= $totalExecutedOperations;
            $batch->addError($errors);
        }

        if ($totalOperations <= $totalExecutedOperations) {
            $batch->setStatus(Batch::STATUS_FINISHED);
        } else {
            $batch->setStatus(Batch::STATUS_ACTIVE);
        }

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
