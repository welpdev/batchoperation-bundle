<?php

namespace Welp\BatchBundle\Model;

/**
 * OperationInterface
 *
 */
interface OperationInterface
{

    /** @var string */
    const STATUS_ACTIVE = 'welp_operation_active';
    /** @var string */
    const STATUS_PENDING = 'welp_operation_pending';
    /** @var string */
    const STATUS_FINISHED = 'welp_operation_finished';
    /** @var string */
    const STATUS_ERROR = 'welp_operation_error';

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
     * @return string
     */
    public function getType();

    /**
     * @param string $type
     *
     * @return self
     */
    public function setType($type);

    /**
     * @return array
     */
    public function getPayload();

    /**
     * @param array $payload
     *
     * @return self
     */
    public function setPayload($payload);

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
}
