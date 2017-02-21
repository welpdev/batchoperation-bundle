<?php

namespace Welp\BatchBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Welp\BatchBundle\Model\Batch;

class BatchErrorEvent extends Event
{
    protected $batch;
    protected $error;

    public function __construct(Batch $batch, $error)
    {
        $this->batch = $batch;
        $this->error = $error;
    }

    public function getBatch()
    {
        return $this->batch;
    }

    public function getError()
    {
        return $this->error;
    }
}
