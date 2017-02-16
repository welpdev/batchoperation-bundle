<?php

namespace Welp\BatchBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

abstract class Batch implements BatchInterface
{

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
     * @var int
     *
     */
    protected $totalExecutedOperations  ;

    /**
     * @var ArrayCollection $errors
     */
    protected $errors;

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
    public function getTotalExecutedOperations()
    {
        return $this->totalExecutedOperations;
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalExecutedOperations($totalExecutedOperations)
    {
        $this->totalExecutedOperations = $totalExecutedOperations;
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
}