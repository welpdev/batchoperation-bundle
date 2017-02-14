<?php

namespace Welp\BatchBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Thread
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OperationRepository");
 * @Serializer\ExclusionPolicy("all")
 */
class Operation
{
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
     * @ORM\Column(name="type", type="string")
     * @Serializer\Expose
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string")
     * @Serializer\Expose
     */
    private $status;

    /**
     * @var array
     *
     * @ORM\Column(name="data", type="array")
     * @Serializer\Expose
     */
    private $data;

    /**
     * @var Batch
     *
     * @ORM\ManyToOne(targetEntity="batch", inversedBy="operations")
     * @ORM\JoinColumn(name="batch_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * @Serializer\Expose
     */
    private $batch;

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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return static
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return static
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @return Batch
     */
    public function getBatch()
    {
        return $this->batch;
    }

    /**
     * @param Batch $batch
     *
     * @return static
     */
    public function setBatch(Batch $batch)
    {
        $this->batch = $batch;
        return $this;
    }
}
