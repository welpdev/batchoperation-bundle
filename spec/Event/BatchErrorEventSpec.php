<?php

namespace spec\Welp\BatchBundle\Event;

use Welp\BatchBundle\Event\BatchEvent;
use Welp\BatchBundle\Event\BatchErrorEvent;
use Welp\BatchBundle\EventListener\OperationListener;
use Welp\BatchBundle\Manager\BatchManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Welp\BatchBundle\Manager\OperationManager;
use Welp\BatchBundle\Model\Batch;
use Welp\BatchBundle\Model\Operation;

use Welp\BatchBundle\WelpBatchEvent;

class BatchErrorEventSpec extends ObjectBehavior
{
    public function let(Batch $batch)
    {
        $batch->setId(1);
        $this->beConstructedWith($batch, 'error', 1);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(BatchErrorEvent::class);
    }

    public function it_should_return_batch(Batch $batch)
    {
        $this->getBatch()->shouldReturn($batch);
    }

    public function it_should_return_operationId()
    {
        $this->getOperationId()->shouldReturn(1);
    }

    public function it_should_return_error()
    {
        $this->getError()->shouldReturn('error');
    }
}
