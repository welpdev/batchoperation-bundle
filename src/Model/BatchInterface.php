<?php

namespace Welp\BatchBundle\Model;

/**
 * BatchInterface
 *
 */
interface BatchInterface
{

    /** @var string */
    const STATUS_ACTIVE = 'welp_batch_active';
    /** @var string */
    const STATUS_PENDING = 'welp_batch_pending';
    /** @var string */
    const STATUS_FINISHED = 'welp_batch_finished';
    /** @var string */
    const STATUS_ERROR = 'welp_batch_error';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     *
     * @return self
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $status
     *
     * @return self
     */
    public function setStatus($status);

    /**
     * @return Operation[]
     */
    public function getOperations();

    /**
     * @param array $operations
     *
     * @return static
     */
    public function setOperations(array $operations);

    /**
     * @param array $operations
     */
    public function addOperations($operation);

    /**
     * @return int
     */
    public function getTotalOperations();

    /**
     * @param int $totalOperations
     *
     * @return self
     */
    public function setTotalOperations($totalOperations);

    /**
     * @return array
     */
    public function getExecutedOperations();

    /**
     * @param array $executedOperations
     *
     * @return self
     */
    public function setExecutedOperations($executedOperations);

    /**
     *
     * @param string $operationId
     */
    public function addExecutedOperations($operationId);

    /**
     * @return array
     */
    public function getErrors();

    /**
     * @param array $errors
     *
     * @return static
     */
    public function setErrors(array $errors);

    /**
     *
     * @param array $error
     */
    public function addError(array $error);


    /**
     * @return string
     */
    public function getGroup();

    /**
     * @param string $group
     */
    public function setGroup($group);
}
