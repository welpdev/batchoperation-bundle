<?php

//src/Acme/DemoBundle/Consumer/UploadPictureConsumer.php

namespace Welp\BatchBundle\Consumer\AMQP;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

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
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository($className);
    }

    public function execute(AMQPMessage $msg)
    {
        $operation = unserialize($msg->body);
        $action = $operation['action'];

        $this->$action();
    }


    public function create($operation)
    {
        $entity = new $this->className();
        $form = $this->container->get('form.factory')->create(new $this->form(), $entity);
        $form->bind($operation->getPayload());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function delete()
    {
        return null;
    }
}
