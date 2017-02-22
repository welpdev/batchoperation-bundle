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
     * @param Batch $batch batch which raise the event
     */
    public function __construct(Batch $batch)
    {
        $this->batch = $batch;
    }

    /**
     * @return BatchInterface
     */
    public function getBatch()
    {
        return $this->batch;
    }
}
