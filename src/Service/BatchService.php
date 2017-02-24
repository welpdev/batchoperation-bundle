<?php

namespace Welp\BatchBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Welp\BatchBundle\Manager\BatchManager;
use Welp\BatchBundle\Manager\OperationManager;
use Welp\BatchBundle\Model\Batch;
use Welp\BatchBundle\Model\Operation;
use Welp\BatchBundle\Model\BatchInterface;
use Welp\BatchBundle\Producer\ProducerInterface;

/**
 * batch Service
 */
class BatchService
{
    /**
     *
     * @var ProducerInterface
     */
    private $producer;

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
     * @param BatchManager $batchManager
     * @param OperationManager $operationManager
     * @param ProducerInterface $producer
     */
    public function __construct($batchManager, $operationManager, $producer)
    {
        $this->operationManager = $operationManager;
        $this->batchManager = $batchManager;
        $this->producer = $producer;
    }

    /**
     * This method is used to create a new batch with an array of operations.
     * It will create the batch, and then, create all the message to the given broker.
     * @param  array  $operations [description]
     * @return [type]             [description]
     */
    public function create(array $operations, $group="")
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
        $batch->setExecutedOperations(array());
        $batch->setGroup($group);

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
            $this->producer->produce($operation, $batch->getId(), $type, $action);
        }
        $this->batchManager->update($batch);


        return $batch;
    }
}
