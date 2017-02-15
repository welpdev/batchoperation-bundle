<?php

namespace Welp\BatchBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Thread
 *
 * @ORM\Table()
 * @ORM\Entity();
 * @Serializer\ExclusionPolicy("all")
 */
class Batch
{

    /** @var string */
    const STATUS_ACTIVE = 'welp_batch_active';
    /** @var string */
    const STATUS_PENDING = 'welp_batch_pending';
    /** @var string */
    const STATUS_FINISHED = 'welp_batch_finished';
    /** @var string */

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Serializer\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string")
     * @Serializer\Expose
     */
    private $status;

    /**
     * @var Operation[]
     *
     * @ORM\Column(name="operations", type="array")
     * @Serializer\Expose
     */
    protected $operations;

    /**
     * @var int
     *
     * @ORM\Column(name="total_operations", type="integer")
     * @Serializer\Expose
     */
    protected $totalOperations;

    /**
     * @var int
     *
     * @ORM\Column(name="total_executed_operations", type="integer")
     * @Serializer\Expose
     */
    protected $totalExecutedOperations  ;

    /**
     * @var Errors[]
     *
     * @ORM\Column(name="errors", type="array")
     * @Serializer\Expose
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
     * @param int $id
     *
     * @return static
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return static
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return Operation[]
     */
    public function getOperations()
    {
        return $this->operations;
    }

    /**
     * @param Operation[] $operations
     *
     * @return static
     */
    public function setOperations(array $operations)
    {
        $this->operations = $operations;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalOperations()
    {
        return $this->totalOperations;
    }

    /**
     * @param int $totalOperations
     *
     * @return static
     */
    public function setTotalOperations($totalOperations)
    {
        $this->totalOperations = $totalOperations;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalExecutedOperations()
    {
        return $this->totalExecutedOperations;
    }

    /**
     * @param int $totalOperations
     *
     * @return static
     */
    public function setTotalExecutedOperations($totalExecutedOperations)
    {
        $this->totalExecutedOperations = $totalExecutedOperations;
        return $this;
    }

    /**
     * @return Errors[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param Operation[] $operations
     *
     * @return static
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
        return $this;
    }

    public function addError(array $error)
    {
        $this->errors[] = $error;
    }
}
