<?php

namespace Welp\BatchBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Welp\BatchBundle\Model\Operation;

class OperationEvent extends Event
{
    protected $operation;

    public function __construct(Operation $operation)
    {
        $this->operation = $operation;
    }


    public function getOperation()
    {
        return $this->operation;
    }
}
