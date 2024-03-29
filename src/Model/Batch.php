<?php

namespace Welp\BatchBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Welp\BatchBundle\Model\Traits\TimeStampableTrait;

class Batch implements BatchInterface
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
     * @var ArrayCollection $operations
     */
    protected $operations;

    /**
     * @var int
     */
    protected $totalOperations;

    /**
     * @var array
     *
     */
    protected $executedOperations  ;

    /**
     * @var ArrayCollection $errors
     */
    protected $errors;

    /**
     * @var string
     */
    protected $group;

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
    public function getOperations()
    {
        return $this->operations;
    }

    /**
     * {@inheritdoc}
     */
    public function setOperations(array $operations)
    {
        $this->operations = $operations;
        return $this;
    }

    public function addOperations($operation)
    {
        $this->operations[] = $operation;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalOperations()
    {
        return $this->totalOperations;
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalOperations($totalOperations)
    {
        $this->totalOperations = $totalOperations;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExecutedOperations()
    {
        return $this->executedOperations;
    }

    /**
     * {@inheritdoc}
     */
    public function addExecutedOperations($operationId)
    {
        $this->executedOperations[] = $operationId;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setExecutedOperations($executedOperation)
    {
        $this->executedOperations = $executedOperation;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getErrors()
    {
        if ($this->errors == null) {
            return array();
        }

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


    /**
     * {@inheritdoc}
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * {@inheritdoc}
     */
    public function setGroup($group)
    {
        $this->group = $group;
        return $this;
    }
}
