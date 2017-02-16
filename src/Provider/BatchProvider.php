<?php

namespace Welp\BatchBundle\Provider;

use Welp\BatchBundle\Model\Batch;
use Welp\BatchBundle\Model\Operation;

/**
 * batch Factory
 */
class BatchProvider
{
    private $entityManager;
    private $container;
    private $operationManager;
    private $batchManager;

    public function __construct($entityManager, $container, $batchManager, $operationManager)
    {
        $this->container = $container;
        $this->entityManager = $this->container->get($entityManager);
        $this->operationManager = $operationManager;
        $this->batchManager = $batchManager;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $operations)
    {
        //check if operations is well formatted ( it must have a type in each "row")
        foreach ($operations as $operation) {
            if (!array_key_exists('type', $operation) || !array_key_exists('action', $operation)) {
                throw new \Exception('all rows must have a "type" key');
            }
        }

        $batch = $this->batchManager->createNew();
        $batch->setStatus(Batch::STATUS_PENDING);
        $batch->setTotalOperations(count($operations));
        $batch->setTotalExecutedOperations(0);
        $this->batchManager->create($batch);

        foreach ($operations as $ope) {
            $operation = $this->operationManager->createNew();
            $operation->setType($ope['type']);
            $operation->setStatus(Operation::STATUS_PENDING);

            $type = $ope['type'];
            $action = $ope['action'];

            unset($ope['type']);
            $operation->setPayload($ope);
            $operation->setBatch($batch);
            $batch->addOperations($operation);
            $this->container->get('welp_batch.producer')->produce($ope, $batch->getId(), $type, $action);
        }

        $this->batchManager->update($batch);


        //produce the operations to rmq


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
    public function update($id, $errors)
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

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $batch = $this->batchRepository->findOneById($id);
        $this->entityManager->remove($batch);
        $this->entityManager->flush();
    }
}
