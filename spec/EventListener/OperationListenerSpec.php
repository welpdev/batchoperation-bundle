<?php

namespace spec\Welp\BatchBundle\EventListener;

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

class OperationListenerSpec extends ObjectBehavior
{
    public function let(OperationManager $operationManager, BatchManager $batchManager)
    {
        $this->beConstructedWith($operationManager, $batchManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(OperationListener::class);
    }

    public function it_should_return_three_event()
    {
        $this::getSubscribedEvents()->shouldReturn([
            WelpBatchEvent::WELP_BATCH_OPERATION_STARTED => 'startOperation',
            WelpBatchEvent::WELP_BATCH_OPERATION_FINISHED => 'finishOperation',
            WelpBatchEvent::WELP_BATCH_OPERATION_ERROR => 'errorOperation',
        ]);
    }

    public function it_should_start_operation(BatchManager $batchManager)
    {
        $batch = new Batch();
        $batch->setId(1);
        $batch->setStatus(Batch::STATUS_PENDING);

        $event = new BatchEvent($batch, 1, null);

        $batchManager->update($batch)->shouldBeCalled();
        $this->startOperation($event);
    }

    public function it_should_not_start_operation(BatchManager $batchManager)
    {
        $batch = new Batch();
        $batch->setId(1);
        $batch->setStatus(Batch::STATUS_ACTIVE);

        $event = new BatchEvent($batch, 1, null);

        $batchManager->update($batch)->shouldNotBeCalled();
        $this->startOperation($event);
    }

    public function it_should_finished_operation(BatchManager $batchManager)
    {
        $batch = new Batch();
        $batch->setId(1);
        $batch->setStatus(Batch::STATUS_ACTIVE);
        $batch->setExecutedOperations([]);
        $batch->setTotalOperations(10);

        $event = new BatchEvent($batch, 1, null);

        $batchManager->formatMessage(1, null)->shouldBeCalled()->willReturn(array('operationId' => 1));
        $batchManager->addExecutedOperation($batch, array('operationId' => 1))->willReturn($batch);

        $this->finishOperation($event);
    }

    public function it_should_finished_batch_without_message(BatchManager $batchManager)
    {
        $batch = new Batch();
        $batch->setId(1);
        $batch->setStatus(Batch::STATUS_ACTIVE);
        $batch->setExecutedOperations([]);

        $event = new BatchEvent($batch, 1, null);

        $batchManager->formatMessage(1, null)->shouldBeCalled()->willReturn(array('operationId' => 1));
        $batchManager->addExecutedOperation($batch, array('operationId' => 1))->willReturn($batch);
        $batchManager->update($batch)->shouldBeCalled();
        $batchManager->generateResults($batch)->shouldBeCalled();

        $this->finishOperation($event);
    }

    public function it_should_finished_batch_with_message(BatchManager $batchManager)
    {
        $batch = new Batch();
        $batch->setId(1);
        $batch->setStatus(Batch::STATUS_ACTIVE);
        $batch->setExecutedOperations([]);

        $event = new BatchEvent($batch, 1, 'test');

        $batchManager->formatMessage(1, 'test')->shouldBeCalled()->willReturn(array('operationId' => 1,'message'=>'test'));
        $batchManager->addExecutedOperation($batch, array('operationId' => 1,'message'=>'test'))->willReturn($batch);
        $batchManager->update($batch)->shouldBeCalled();
        $batchManager->generateResults($batch)->shouldBeCalled();

        $this->finishOperation($event);
    }

    public function it_should_error_operation(BatchManager $batchManager)
    {
        $batch = new Batch();
        $batch->setId(1);
        $batch->setStatus(Batch::STATUS_PENDING);
        $batch->setTotalOperations(10);

        $event= new BatchErrorEvent($batch, 'error', 1);

        $batchManager->update($batch)->shouldBeCalled();

        $batchManager->formatMessage(1, 'error')->shouldBeCalled()->willReturn(array('operationId' => 1));
        $batchManager->addExecutedOperation($batch, array('operationId' => 1))->willReturn($batch);

        $this->errorOperation($event);
    }

    public function it_should_error_operation_and_finished_batch(BatchManager $batchManager)
    {
        $batch = new Batch();
        $batch->setId(1);
        $batch->setStatus(Batch::STATUS_PENDING);
        //$batch->setTotalOperations(10);

        $event= new BatchErrorEvent($batch, 'error', 1);

        $batchManager->update($batch)->shouldBeCalled();

        $batchManager->formatMessage(1, 'error')->shouldBeCalled()->willReturn(array('operationId' => 1, 'message'=>'error'));
        $batchManager->addExecutedOperation($batch, array('operationId' => 1, 'message'=>'error'))->willReturn($batch);

        $batchManager->update($batch)->shouldBeCalled();
        $batchManager->generateResults($batch)->shouldBeCalled();

        $this->errorOperation($event);
    }
}
