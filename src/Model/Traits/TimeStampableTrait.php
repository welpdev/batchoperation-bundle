<?php

namespace Welp\BatchBundle\Model\Traits;

use DateTime;

/**
 */
trait TimeStampableTrait
{
    /**
     *
     * @var DateTime
     */
    protected $createdAt;

    /**
     *
     * @var DateTime
     */
    protected $updatedAt;

    /**
     *
     * @var DateTime
     */
    protected $startedAt;

    /**
     *
     * @var DateTime
     */
    protected $finishedAt;

    /**
     * Sets createdAt.
     *
     * @param  \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Returns createdAt.
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Sets updatedAt.
     *
     * @param  \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Returns updatedAt.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return
     */
    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    /**
     * @param  $finishedAt
     *
     * @return static
     */
    public function setFinishedAt($finishedAt)
    {
        $this->finishedAt = $finishedAt;
        return $this;
    }

    /**
     * @return
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * @param  $startedAt
     *
     * @return static
     */
    public function setStartedAt($startedAt)
    {
        $this->startedAt = $startedAt;
        return $this;
    }
}
