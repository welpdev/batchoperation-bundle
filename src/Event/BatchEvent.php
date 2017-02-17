<?php

namespace Welp\BatchBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Welp\BatchBundle\Model\Batch;

class BatchEvent extends Event
{
    protected $batch;

    public function __construct(Batch $batch)
    {
        $this->batch = $batch;
    }


    public function getBatch()
    {
        return $this->batch;
    }
}
