<?php

//src/Acme/DemoBundle/Consumer/UploadPictureConsumer.php

namespace Welp\BatchBundle\Consumer\AMQP;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Welp\BatchBundle\WelpBatchEvent;
use Welp\BatchBundle\Event\OperationEvent;

class RabbitMQConsumer implements ConsumerInterface
{
    private $container;
    private $className;
    private $form;
    private $entityManager;
    private $repository;

    public function __construct($container, $className, $form, $entityManager)
    {
        $this->container = $container;
        $this->className = $className;
        $this->form = $form;
        $this->entityManager = $this->container->get($entityManager);
        $this->repository = $this->entityManager->getRepository($className);
    }

    public function execute(AMQPMessage $msg)
    {
        $temp = unserialize($msg->body);
        $operation = $this->container->get('welp_batch.operation_manager')->get($temp['operationId']);

        $event = new OperationEvent($operation);
        $this->container->get('event_dispatcher')->dispatch(WelpBatchEvent::WELP_BATCH_OPERATION_STARTED, $event);

        $action = $temp['action'];

        $this->$action($operation);

        $this->container->get('event_dispatcher')->dispatch(WelpBatchEvent::WELP_BATCH_OPERATION_FINISHED, $event);
    }


    public function create($operation)
    {
        $entity = new $this->className();
        $form = $this->container->get('form.factory')->create(new $this->form(), $entity);
        $form->bind($operation->getPayload());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function delete($operation)
    {
        $id = $operation->getPayload()['id'];
        $entity = $this->repository->findOneById($id);

        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
