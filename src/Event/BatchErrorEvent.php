<?php

namespace Welp\BatchBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Welp\BatchBundle\Model\Batch;
use Welp\BatchBundle\Model\BatchInterface;

/**
 * Event throw when an operation in a batch is raised
 */
class BatchErrorEvent extends Event
{
    /**
     * @var BatchInterface
     */
    protected $batch;

    /**
     * @var String
     */
    protected $error;

    /**
     * @var integer
     */
    protected $operationId;

    /**
     * @param Batch  $batch       Batch that raise an error
     * @param String $error       Message of the error
     * @param integer $operationId id of the operation which throw an error
     */
    public function __construct(Batch $batch, $error, $operationId)
    {
        $this->batch = $batch;
        $this->error = $error;
        $this->operationId = $operationId;
    }

    /**
     * @return Batch
     */
    public function getBatch()
    {
        return $this->batch;
    }

    /**
     * @return String
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return integer
     */
    public function getOperationId()
    {
        return $this->operationId;
    }
}
