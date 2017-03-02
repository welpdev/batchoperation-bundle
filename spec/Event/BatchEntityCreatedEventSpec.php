<?php

namespace spec\Welp\BatchBundle\Event;

use Welp\BatchBundle\Event\BatchEvent;
use Welp\BatchBundle\Event\BatchErrorEvent;
use Welp\BatchBundle\Event\BatchEntityCreatedEvent;
use Welp\BatchBundle\EventListener\OperationListener;
use Welp\BatchBundle\Manager\BatchManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Welp\BatchBundle\Manager\OperationManager;
use Welp\BatchBundle\Model\Batch;
use Welp\BatchBundle\Model\Operation;

use Welp\BatchBundle\WelpBatchEvent;

class BatchEntityCreatedEventSpec extends ObjectBehavior
{
    public function let(Batch $batch)
    {
        $batch->setId(1);
        $this->beConstructedWith($batch, 'Welp\BatchBundle\Model\Batch');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(BatchEntityCreatedEvent::class);
    }

    public function it_should_return_entity(Batch $batch)
    {
        $this->getEntity()->shouldReturn($batch);
    }

    public function it_should_return_operationId()
    {
        $this->getClassName()->shouldReturn('Welp\BatchBundle\Model\Batch');
    }
}
