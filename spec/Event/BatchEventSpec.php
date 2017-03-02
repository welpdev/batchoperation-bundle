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

class BatchEventSpec extends ObjectBehavior
{
    public function let(Batch $batch)
    {
        $batch->setId(1);
        $this->beConstructedWith($batch, 1, 'test');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(BatchEvent::class);
    }

    public function it_should_return_batch(Batch $batch)
    {
        $this->getBatch()->shouldReturn($batch);
    }

    public function it_should_return_operationId()
    {
        $this->getOperationId()->shouldReturn(1);
    }

    public function it_should_return_consumer_message()
    {
        $this->getConsumerMessage()->shouldReturn('test');
    }
}
