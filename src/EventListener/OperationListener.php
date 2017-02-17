<?php

namespace Welp\BatchBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Welp\BatchBundle\WelpBatchEvent;
use Welp\BatchBundle\Event\OperationEvent;
use Welp\BatchBundle\Event\OperationErrorEvent;
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

    public function startOperation(OperationEvent $event)
    {
        $operation = $event->getOperation();
        $operation->setStatus(Operation::STATUS_ACTIVE);
        $this->operationManager->update($operation);

        $batch = $operation->getBatch();
        if ($batch->getStatus() != Batch::STATUS_ACTIVE) {
            $batch->setStatus(Batch::STATUS_ACTIVE);
            $this->batchManager->update($batch);
        }
    }

    public function finishOperation(OperationEvent $event)
    {
        $operation = $event->getOperation();
        $operation->setStatus(Operation::STATUS_FINISHED);

        //MAJ BATCH
        $batch = $operation->getBatch();

        $totalOperations = $batch->getTotalOperations();
        $totalExecutedOperations = $batch->getTotalExecutedOperations();

        $totalExecutedOperations+=1;
        $batch->setTotalExecutedOperations($totalExecutedOperations);
        if ($totalOperations <= $totalExecutedOperations) {
            $batch->setStatus(Batch::STATUS_FINISHED);
        }

        $this->operationManager->update($operation);
        $this->batchManager->update($batch);
    }

    public function errorOperation(OperationErrorEvent $event)
    {
        $operation = $event->getOperation();
        $operation->setStatus(Operation::STATUS_ERROR);
        $temp = array(
            'error'=>$event->getError()
        );
        $operation->setErrors($temp);
        $this->operationManager->update($operation);
    }
}
