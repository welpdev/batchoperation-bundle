<?php

namespace Welp\BatchBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Welp\BatchBundle\WelpBatchEvent;
use Welp\BatchBundle\Event\BatchEvent;
use Welp\BatchBundle\Event\OperationEvent;
use Welp\BatchBundle\Event\OperationErrorEvent;
use Welp\BatchBundle\Event\BatchErrorEvent;
use Welp\BatchBundle\Model\Operation;
use Welp\BatchBundle\Model\Batch;

/**
 * Recalculate average when new evaluation
 *
 */
class OperationListener implements EventSubscriberInterface
{
    private $operationManager;
    private $batchManager;

    public function __construct($operationManager, $batchManager)
    {
        $this->operationManager = $operationManager;
        $this->batchManager = $batchManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            WelpBatchEvent::WELP_BATCH_OPERATION_STARTED => 'startOperation',
            WelpBatchEvent::WELP_BATCH_OPERATION_FINISHED => 'finishOperation',
            WelpBatchEvent::WELP_BATCH_OPERATION_ERROR => 'errorOperation',
        ];
    }

    public function startOperation(BatchEvent $event)
    {
        $batch = $event->getBatch();
        /*$operation->setStatus(Operation::STATUS_ACTIVE);
        $operation->setStartedAt(new \DateTime());
        $this->operationManager->update($operation);*/

        //$batch = $operation->getBatch();
        if ($batch->getStatus() != Batch::STATUS_ACTIVE) {
            $batch->setStatus(Batch::STATUS_ACTIVE);
            $batch->setStartedAt(new \DateTime());
            $this->batchManager->update($batch);
        }
    }

    public function finishOperation(BatchEvent $event)
    {
        /*$operation = $event->getBatch();
        $operation->setStatus(Operation::STATUS_FINISHED);
        $operation->setFinishedAt(new \DateTime());
        $this->operationManager->update($operation);*/

        //update BATCH
        $batch = $event->getBatch();
        $this->updatebatch($batch);
    }

    public function errorOperation(BatchErrorEvent $event)
    {
        $batch = $event->getBatch();
        //$operation->setStatus(Operation::STATUS_ERROR);
        //$operation->setFinishedAt(new \DateTime());
        $temp = array(
            'error'=>$event->getError(),
            'operationId'=> $event->getOperationId()
        );
        //$operation->setErrors($temp);
        //$this->operationManager->update($operation);
        $batch->addError($temp);
        //$batch = $operation->getBatch();
        $this->updatebatch($batch);
    }

    public function updateBatch($batch)
    {
        $totalOperations = $batch->getTotalOperations();
        $totalExecutedOperations = $batch->getTotalExecutedOperations();

        $totalExecutedOperations+=1;
        $batch->setTotalExecutedOperations($totalExecutedOperations);

        if ($totalOperations <= $totalExecutedOperations) { //all operations have been executed
            $batch->setStatus(Batch::STATUS_FINISHED);
            $batch->setFinishedAt(new \DateTime());

            //write the responses to a file TODO check if this is the right thing to do
            $this->batchManager->generateResults($batch);
        }

        $this->batchManager->update($batch);
    }
}
