<?php

namespace Welp\BatchBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Welp\BatchBundle\Model\Batch;

class BatchErrorEvent extends Event
{
    protected $batch;
    protected $error;
    protected $operationId;

    public function __construct(Batch $batch, $error, $operationId)
    {
        $this->batch = $batch;
        $this->error = $error;
        $this->operationId = $operationId;
    }

    public function getBatch()
    {
        return $this->batch;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getOperationId()
    {
        return $this->operationId;
    }
}
