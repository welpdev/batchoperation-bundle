<?php

use OldSound\RabbitMqBundle\Tests\RabbitMq\ConsumerTest as baseTestRMQ;
use PhpAmqpLib\Message\AMQPMessage;

class ConsumerTest extends baseTestRMQ
{
    private $index = 0;

    protected function prepareCallBack()
    {
        return $this->getMockBuilder('Welp\BatchBundle\Consumer\AMQP\RabbitMQConsumer')
                        ->disableOriginalConstructor()
                        ->getMock();
    }
    /**
     * @dataProvider consumeMessage
     * @param $data
     */
    public function testConsumeMessage($data)
    {
        //var_dump(array_splice($data, 0, 1));
        $consumerCallBacks = $data;
        $amqpConnection = $this->prepareAMQPConnection();
        $amqpChannel = $this->prepareAMQPChannel();

        $amqpChannel->expects($this->atLeastOnce())
            ->method('getChannelId')
            ->with()
            ->willReturn(true);
        $amqpChannel->expects($this->once())
            ->method('basic_consume')
            ->withAnyParameters()
            ->willReturn(true);

        $consumer = $this->getConsumer($amqpConnection, $amqpChannel);

        $callBack = $this->prepareCallBack();

        $consumer->disableAutoSetupFabric();
        $consumer->setChannel($amqpChannel);
        $amqpChannel->callbacks = $consumerCallBacks;
        $consumer->setCallBack($callBack);

        $callBack->expects($this->once())
            ->method('execute')
            ->withAnyParameters()
            ->willReturn(true);

        $amqpChannel->expects($this->exactly(0))
            ->method('wait')
            ->with(null, false, $consumer->getIdleTimeout())
            ->will($this->returnCallback($callBack->execute(new AMQPMessage(serialize(array_splice($amqpChannel->callbacks, 0, 1)[0]))))
        );
        $consumer->consume(1);
    }

    /**
     * @return array
     */
    public function consumeMessage()
    {
        $testCases[ "test"] =  array(
            array(
                array(
                    "batchId" =>1,
                    "operationId" =>1,
                    "operationPayload"=> array('test'=>'test'),
                    "type"=>"test",
                    "action" => "create"
                )
            )
        );
        return $testCases;
    }
}
