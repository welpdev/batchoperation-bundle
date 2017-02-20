<?php

namespace Welp\BatchBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Welp\BatchBundle\Model\Traits\TimeStampableTrait;

/**
 * Operation
 */
abstract class Operation implements OperationInterface
{
    use TimeStampableTrait;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $payload;

    /**
     * @var ArrayCollection $errors
     */
    protected $errors;

    protected $batch;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * {@inheritdoc}
     */
    public function setPayload($payload)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * {@inheritdoc}
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addError(array $error)
    {
        $this->errors[] = $error;
    }

    public function getBatch()
    {
        return $this->batch;
    }

    public function setBatch($batch)
    {
        $this->batch = $batch;
    }
}
