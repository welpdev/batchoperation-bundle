<?php

namespace Welp\BatchBundle\Exception;

use Exception;
use Welp\BatchBundle\Model\Operation;

class OperationException extends \Exception
{

    /**
     * @var Operation
     */
    private $operation;

    /**
     * @param int    $code
     * @param string $message
     * @param Operation $entity
     */
    public function __construct($code=0, $message, $entity = null)
    {
        parent::__construct($message, $code);

        $this->operation = $entity;
    }

    /**
     * @return Need
     */
    public function getEntity()
    {
        return $this->operation;
    }
}
