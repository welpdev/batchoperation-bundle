<?php

namespace spec\Welp\BatchBundle\Service;

use Welp\BatchBundle\Manager\BatchManager;
use Welp\BatchBundle\Manager\OperationManager;
use Welp\BatchBundle\Model\Batch;
use Welp\BatchBundle\Model\Operation;
use Welp\BatchBundle\Service\BatchService;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ServiceContainer;

use Welp\BatchBundle\Producer\ProducerInterface;

class BatchServiceSpec extends ObjectBehavior
{
    public function let(ServiceContainer $container, ObjectManager $em, BatchManager $batchManager, OperationManager $operationManager)
    {
        $container->get("doctrine.orm.entity_manager")->willReturn($em);

        $this->beConstructedWith('doctrine.orm.entity_manager', $container, $batchManager, $operationManager);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(BatchService::class);
    }

    public function it_can_create_batch_without_operation(BatchManager $batchManager, ServiceContainer $container, ObjectManager $em)
    {
        $batch= new Batch();
        $id =1;
        $batch->setId($id);
        $batchManager->createNew()->willreturn($batch);
        $batchManager->create($batch)->willReturn($batch);
        $batchManager->update($batch)->willReturn($batch);

        $batchManager->createNew()->shouldBeCalled();
        $batchManager->create($batch)->shouldBeCalled();
        $batchManager->update($batch)->shouldBeCalled();
        $this->create(array())->shouldhaveType(Batch::class);
    }

    public function it_can_create_batch_with_operation(BatchManager $batchManager, OperationManager $operationManager, ServiceContainer $container, ObjectManager $em, ProducerInterface $producer)
    {
        $batch= new Batch();
        $id =1;
        $batch->setId($id);
        $batchManager->createNew()->willreturn($batch);
        $batchManager->create($batch)->willReturn($batch);
        $batchManager->update($batch)->willReturn($batch);

        $operation = new Operation();
        $operationManager->createNew()->willreturn($operation);
        $operationManager->create()->willreturn($operation);

        $batchManager->createNew()->shouldBeCalled();
        $batchManager->create($batch)->shouldBeCalled();
        $batchManager->update($batch)->shouldBeCalled();

        $operationManager->createNew()->shouldBeCalled();
        $operationManager->create($operation)->shouldBeCalled();

        $container->get('welp_batch.producer')->willReturn($producer);
        $producer->produce($operation, $batch->getId(), 'test', 'create')->willReturn(true);

        $this->create(array(
            array(
                'type' => 'test',
                'action' => 'create',
                'payload' => array('test'=>'test')
            )
        ))->shouldhaveType(Batch::class);
    }
}
