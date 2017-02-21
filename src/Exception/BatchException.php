<?php

namespace Welp\BatchBundle\Exception;

use Exception;
use Welp\BatchBundle\Model\Operation;
use Welp\BatchBundle\Model\BatchInterface;

class BatchException extends \Exception
{

    /**
     * @var Operation
     */
    private $batch;

    /**
     * @param int    $code
     * @param string $message
     * @param BatchInterface $entity
     */
    public function __construct($code=0, $message, $entity = null)
    {
        parent::__construct($message, $code);

        $this->batch = $entity;
    }

    /**
     * @return Need
     */
    public function getEntity()
    {
        return $this->batch;
    }
}
