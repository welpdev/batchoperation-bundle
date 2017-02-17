<?php

namespace Welp\BatchBundle\Consumer\AMQP;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Welp\BatchBundle\WelpBatchEvent;
use Welp\BatchBundle\Event\OperationEvent;
use Welp\BatchBundle\Event\OperationErrorEvent;
use Welp\BatchBundle\Exception\OperationException;

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

        try {
            $this->$action($operation);
        } catch (OperationException $e) {
            $event = new OperationErrorEvent($operation, $e->getMessage());
            $this->container->get('event_dispatcher')->dispatch(WelpBatchEvent::WELP_BATCH_OPERATION_ERROR, $event);
            return true;
        }

        $this->container->get('event_dispatcher')->dispatch(WelpBatchEvent::WELP_BATCH_OPERATION_FINISHED, $event);
    }


    public function create($operation)
    {
        $entity = new $this->className();
        $form = $this->container->get('form.factory')->create(new $this->form(), $entity);
        $form->bind($operation->getPayload());

        if (!$form->isValid()) {
            throw new OperationException(400, 'Form Error, check yout payload', $operation);
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function delete($operation)
    {
        $id = $operation->getPayload()['id'];
        $entity = $this->repository->findOneById($id);

        if ($entity == null) {
            throw new OperationException(404, $tis->className.' not found', $operation);
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
