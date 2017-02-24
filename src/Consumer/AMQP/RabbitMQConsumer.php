<?php

namespace Welp\BatchBundle\Consumer\AMQP;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Welp\BatchBundle\WelpBatchEvent;
use Welp\BatchBundle\Event\BatchEvent;
use Welp\BatchBundle\Event\OperationEvent;
use Welp\BatchBundle\Event\BatchErrorEvent;
use Welp\BatchBundle\Event\OperationErrorEvent;
use Welp\BatchBundle\Event\BatchEntityDeletedEvent;
use Welp\BatchBundle\Event\BatchEntityCreatedEvent;
use Welp\BatchBundle\Exception\BatchException;
use Welp\BatchBundle\Exception\OperationException;
use Welp\BatchBundle\Model\BatchInterface;

/**
 * WelpBatch Consumer class when using the rabbitMQ broker Type
 */
class RabbitMQConsumer implements ConsumerInterface
{
    /**
     * @var ContainerAwareInterface $container
     */
    private $container;
    /**
     * @var String $className
     */
    private $className;

    /**
     * @var String $form
     */
    private $form;

    /**
     * @var EntityManager $entityManager
     */
    private $entityManager;

    /**
     * @var EntityRepository $repository
     */
    private $repository;

    /**
     * COnstructor of the consumer. This function is called when initializing the service
     * @param ContainerAwareInterface $container     Service Container
     * @param String $className     Fullname of the class the operation will create
     * @param String $form          Fullname of the form class to use for binding data to the new entity
     * @param String $entityManager Name of the entityManager service
     */
    public function __construct($container, $className, $form, $entityManager)
    {
        $this->container = $container;
        $this->className = $className;
        $this->form = $form;
        $this->entityManager = $this->container->get($entityManager);
        $this->repository = $this->entityManager->getRepository($className);
    }

    /**
     * Execute function. This one is launch when the consumer is launched
     * @param  AMQPMessage $msg message receive from RabbitMQ
     * @return Bool           true if sucess, false otherwise
     */
    public function execute(AMQPMessage $msg)
    {
        $temp = unserialize($msg->body);

        $operationPayload = $temp['operationPayload'];

        $batch = $this->container->get('welp_batch.batch_manager')->get($temp['batchId']);

        $event = new BatchEvent($batch, $temp['operationId']);
        $this->container->get('event_dispatcher')->dispatch(WelpBatchEvent::WELP_BATCH_OPERATION_STARTED, $event);

        $action = $temp['action'];

        try {
            $message = $this->$action($operationPayload, $batch);
        } catch (BatchException $e) {
            $event = new BatchErrorEvent($batch, $e->getMessage(), $temp['operationId']);
            $this->container->get('event_dispatcher')->dispatch(WelpBatchEvent::WELP_BATCH_OPERATION_ERROR, $event);
            return true;
        }
        $event = $event = new BatchEvent($batch, $temp['operationId'], $message);
        $this->container->get('event_dispatcher')->dispatch(WelpBatchEvent::WELP_BATCH_OPERATION_FINISHED, $event);
        return true;
    }

    /**
     * Function use to create an entity
     * @param  array $operationPayload Payload containing the parameters to bind the new entity
     * @param  BatchInterface $batch            Batch containing the current operation
     */
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

        $eventCreated = new BatchEntityCreatedEvent($entity, $this->className);
        $this->container->get('event_dispatcher')->dispatch(WelpBatchEvent::WELP_BATCH_ENTITY_CREATED, $eventCreated);

        return array(
            'message' => $this->className.' created',
            'id' => $entity->getId()
        );
    }

    /**
     * Function use to delete an entity
     * @param  array $operationPayload Payload containing the parameters to delete the entity
     * @param  BatchInterface $batch            Batch containing the current operation
     */
    public function delete($operationPayload, $batch)
    {
        $id = $operationPayload['id'];
        $entity = $this->repository->findOneById($id);

        if ($entity == null) {
            throw new BatchException(404, $this->className.' not found', $batch);
        }

        $eventDeleted = new BatchEntityDeletedEvent($entity, $this->className);
        $this->container->get('event_dispatcher')->dispatch(WelpBatchEvent::WELP_BATCH_ENTITY_DELETED, $eventDeleted);

        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        return array(
            'message' => $this->className.' deleted',
            'id' => $id
        );
    }
}
