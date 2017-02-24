<?php

namespace Welp\BatchBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Welp\BatchBundle\Model\Batch;
use Welp\BatchBundle\Model\BatchInterface;

/**
 * Event raise when an operation of a batch is started/finished
 */
class BatchEvent extends Event
{
    /**
     * @var BatchInterface
     */
    protected $batch;

    /**
     * @var integer
     */
    protected $operationId;

    /**
     * @var mixed
     */
    protected $consumerMessage;
    /**
     * @param Batch $batch batch which raise the event
     */
    public function __construct(Batch $batch, $operationId, $consumerMessage=null)
    {
        $this->batch = $batch;
        $this->operationId = $operationId;
        $this->consumerMessage = $consumerMessage;
    }

    /**
     * @return BatchInterface
     */
    public function getBatch()
    {
        return $this->batch;
    }

    public function getOperationId()
    {
        return $this->operationId;
    }

    public function getConsumerMessage()
    {
        return $this->consumerMessage;
    }
}
