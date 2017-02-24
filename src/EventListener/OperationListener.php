<?php

namespace Welp\BatchBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Welp\BatchBundle\WelpBatchEvent;
use Welp\BatchBundle\Event\BatchEvent;
use Welp\BatchBundle\Event\OperationEvent;
use Welp\BatchBundle\Event\OperationErrorEvent;
use Welp\BatchBundle\Event\BatchErrorEvent;
use Welp\BatchBundle\Manager\BatchManager;
use Welp\BatchBundle\Manager\OperationManager;
use Welp\BatchBundle\Model\Operation;
use Welp\BatchBundle\Model\Batch;
use Welp\BatchBundle\Model\BatchInterface;

/**
 * Listener of all operation Event
 *
 */
class OperationListener implements EventSubscriberInterface
{
    /**
     * @var OperationManager
     */
    private $operationManager;

    /**
     * @var BatchManager
     */
    private $batchManager;

    /**
     *
     * @param OperationManager $operationManager
     * @param BatchManager $batchManager
     */
    public function __construct($operationManager, $batchManager)
    {
        $this->operationManager = $operationManager;
        $this->batchManager = $batchManager;
    }

    /**
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            WelpBatchEvent::WELP_BATCH_OPERATION_STARTED => 'startOperation',
            WelpBatchEvent::WELP_BATCH_OPERATION_FINISHED => 'finishOperation',
            WelpBatchEvent::WELP_BATCH_OPERATION_ERROR => 'errorOperation',
        ];
    }

    /**
     * Listener when an operation is launched
     * @param  BatchEvent $event
     */
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

    /**
     * Listener when an operation is finished
     * @param  BatchEvent $event
     */
    public function finishOperation(BatchEvent $event)
    {
        $batch = $event->getBatch();
        $this->updatebatch($batch, $event->getOperationId(), $event->getConsumerMessage());
    }

    /**
     * Listener when error is raised by an operation
     * @param  BatchErrorEvent $event
     */
    public function errorOperation(BatchErrorEvent $event)
    {
        $batch = $event->getBatch();
        $temp = array(
            'error'=>$event->getError(),
            'operationId'=> $event->getOperationId()
        );
        $batch->addError($temp);
        $this->batchManager->update($batch);
        $this->updatebatch($batch, $event->getOperationId(), $event->getError());
    }

    /**
     * This function is used when we went to update the status of a batch
     * @param  BatchInterface $batch
     */
    public function updateBatch($batch, $operationId, $consumerMessage=null)
    {
        $totalOperations = $batch->getTotalOperations();

        $message = $this->batchManager->formatMessage($operationId, $consumerMessage);
        $batch = $this->batchManager->addExecutedOperation($batch, $message);

        $totalExecutedOperations = count($batch->getExecutedOperations());

        if ($totalOperations <= $totalExecutedOperations) { //all operations have been executed
            $batch->setStatus(Batch::STATUS_FINISHED);
            $batch->setFinishedAt(new \DateTime());
            $this->batchManager->update($batch);
            //write the responses to a file
            //TODO check if this is the right thing to do
            $this->batchManager->generateResults($batch);
        }
    }
}
