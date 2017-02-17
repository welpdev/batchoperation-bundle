<?php

namespace Welp\BatchBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Welp\BatchBundle\WelpBatchEvent;
use Welp\BatchBundle\Event\OperationEvent;
use Welp\BatchBundle\Model\Operation;

/**
 * Recalculate average when new evaluation
 *
 */
class OperationListener implements EventSubscriberInterface
{
    private $operationManager;

    public function __construct($operationManager)
    {
        $this->operationManager = $operationManager;
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
    }

    public function finishOperation(OperationEvent $event)
    {
        $operation = $event->getOperation();
        $operation->setStatus(Operation::STATUS_FINISHED);
        $this->operationManager->update($operation);
    }

    public function errorOperation(OperationEvent $event)
    {
        $operation = $event->getOperation();
        $operation->setStatus(Operation::STATUS_FINISHED);
        $this->operationManager->update($operation);
    }
}
