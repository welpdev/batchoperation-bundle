<?php

namespace Welp\BatchBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Welp\BatchBundle\Manager\BatchManager;
use Welp\BatchBundle\Manager\OperationManager;
use Welp\BatchBundle\Model\Batch;
use Welp\BatchBundle\Model\Operation;
use Welp\BatchBundle\Model\BatchInterface;

/**
 * batch Service
 */
class BatchService
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     *
     * @var OperationManager
     */
    private $operationManager;

    /**
     *
     * @var BatchManager
     */
    private $batchManager;

    /**
     *
     * @param String $entityManager    Name of the entityManager service
     * @param ContainerInterface $container
     * @param BatchManager $batchManager
     * @param OperationManager $operationManager
     */
    public function __construct($entityManager, $container, $batchManager, $operationManager)
    {
        $this->container = $container;
        $this->entityManager = $this->container->get($entityManager);
        $this->operationManager = $operationManager;
        $this->batchManager = $batchManager;
    }

    /**
     * This method is used to create a new batch with an array of operations.
     * It will create the batch, and then, create all the message to the given broker.
     * @param  array  $operations [description]
     * @return [type]             [description]
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

        $indexOperation = 1;
        foreach ($operations as $ope) {
            $operation = $this->operationManager->createNew();
            $operation->setType($ope['type']);
            $operation->setStatus(Operation::STATUS_PENDING);

            $type = $ope['type'];
            $action = $ope['action'];
            $ope['operationId']=$indexOperation;
            $operation->setId($indexOperation);

            $indexOperation+=1;

            $operation->setPayload($ope['payload']);
            $batch->addOperations($ope);
            $this->operationManager->create($operation);
            $this->container->get('welp_batch.producer')->produce($operation, $batch->getId(), $type, $action);
        }
        $this->batchManager->update($batch);


        return $batch;
    }
}
