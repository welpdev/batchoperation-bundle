<?php

namespace spec\Welp\BatchBundle\Consumer\AMQP;

use Welp\BatchBundle\Consumer\AMQP\RabbitMQConsumer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ServiceContainer;

use PhpAmqpLib\Message\AMQPMessage;

use Welp\BatchBundle\Event\BatchEvent;
use Welp\BatchBundle\Manager\BatchManager;
use Welp\BatchBundle\Model\Batch;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Welp\BatchBundle\WelpBatchEvent;

class RabbitMQConsumerSpec extends ObjectBehavior
{
    public function let(ServiceContainer $container, ObjectManager $em, ObjectRepository $repository)
    {
        $container->get("doctrine.orm.entity_manager")->willReturn($em);
        $em->getRepository("FooBundle\Entity\Test")->willReturn($repository);

        $this->beConstructedWith($container, 'FooBundle\Entity\Test', 'FooBundle\Form\Test', "doctrine.orm.entity_manager");
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(RabbitMQConsumer::class);
    }

    /*public function it_can_create_entity(ServiceContainer $container, BatchManager $batchManager, EventDispatcherInterface $dispatcher)
    {
        $batch = new Batch();
        $batch->setId(1);
        $container->get('welp_batch.batch_manager')->willReturn($batchManager);
        $batchManager->get(1)->willReturn($batch);
        $container->get('event_dispatcher')->willReturn($dispatcher);

        $event = new BatchEvent($batch);

        $dispatcher->dispatch(WelpBatchEvent::WELP_BATCH_OPERATION_STARTED, $event)->shouldBeCalled();

        $temp = array(
            'batchId' => 1,
            'action' => 'create',
            'operationPayload' => array()
        );

        $temp = serialize($temp);

        $message = new AMQPMessage($temp);
        $this->create(array(), $batch)->willReturn(true)->shouldBeCalled();
        $this->execute($message);
    }*/
}
