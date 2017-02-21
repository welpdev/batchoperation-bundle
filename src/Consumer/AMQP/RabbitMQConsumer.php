<?php

namespace Welp\BatchBundle\Consumer\AMQP;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Welp\BatchBundle\WelpBatchEvent;
use Welp\BatchBundle\Event\BatchEvent;
use Welp\BatchBundle\Event\OperationEvent;
use Welp\BatchBundle\Event\BatchErrorEvent;
use Welp\BatchBundle\Event\OperationErrorEvent;
use Welp\BatchBundle\Exception\BatchException;
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

        $operationPayload = $temp['operationPayload'];

        $batch = $this->container->get('welp_batch.batch_manager')->get($temp['batchId']);

        $event = new BatchEvent($batch);
        $this->container->get('event_dispatcher')->dispatch(WelpBatchEvent::WELP_BATCH_OPERATION_STARTED, $event);

        $action = $temp['action'];

        try {
            $this->$action($operationPayload, $batch);
        } catch (BatchException $e) {
            $event = new BatchErrorEvent($batch, $e->getMessage(), $temp['operationId']);
            $this->container->get('event_dispatcher')->dispatch(WelpBatchEvent::WELP_BATCH_OPERATION_ERROR, $event);
            return true;
        }

        $this->container->get('event_dispatcher')->dispatch(WelpBatchEvent::WELP_BATCH_OPERATION_FINISHED, $event);
    }


    public function create($operationPayload, $batch)
    {
        $entity = new $this->className();
        $form = $this->container->get('form.factory')->create(new $this->form(), $entity);
        $form->bind($operationPayload);

        if (!$form->isValid()) {
            throw new BatchException(400, 'Form Error, check yout payload', $batch);
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function delete($operationPayload, $batch)
    {
        $id = $operationPayload['id'];
        $entity = $this->repository->findOneById($id);

        if ($entity == null) {
            throw new BatchException(404, $tis->className.' not found', $batch);
        }

        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
