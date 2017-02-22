<?php

namespace spec\Welp\BatchBundle\Producer\AMQP;

use Welp\BatchBundle\Producer\AMQP\RabbitMQProducer;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use PhpSpec\ServiceContainer;

class RabbitMQProducerSpec extends ObjectBehavior
{
    public function let(ServiceContainer $container)
    {
        $this->beConstructedWith($container);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(RabbitMQProducer::class);
    }

    public function it_should_return_queue_name()
    {
        $this->selectQueue('test', 'create')->shouldReturn('old_sound_rabbit_mq.welp_batch_test_create_producer');
    }
}
