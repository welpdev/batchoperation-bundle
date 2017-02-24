<?php

namespace Welp\BatchBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Welp\BatchBundle\Model\Batch;
use Welp\BatchBundle\Model\BatchInterface;

/**
 * Event raise when an operation of a batch is started/finished
 */
class BatchEntityDeletedEvent extends Event
{
    /**
     * @var mixed
     */
    protected $entity;

    /**
     *
     * @var string
     */
    protected $className;


    /**
     * @param mixed $entity entity deleted
     * @param string $className name of the class of the deleted entity
     */
    public function __construct($entity, $className)
    {
        $this->entity = $entity;
        $this->className = $className;
    }

    /**
     * @return mixed
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
}
