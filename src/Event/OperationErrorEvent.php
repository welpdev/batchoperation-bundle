<?php

namespace Welp\BatchBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Welp\BatchBundle\Model\Operation;

class OperationErrorEvent extends Event
{
    protected $operation;
    protected $error;

    public function __construct(Operation $operation, $error)
    {
        $this->operation = $operation;
        $this->error = $error;
    }

    public function getOperation()
    {
        return $this->operation;
    }

    public function getError()
    {
        return $this->error;
    }
}
