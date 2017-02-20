<?php

namespace Welp\BatchBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class OperationController extends FOSRestController
{
    //TODO api_doc

    /**
    *
    * @Rest\View(serializerEnableMaxDepthChecks=true)
    * @ApiDoc(
    *   resource = true,
    *   description = "Return a single operation with the given id",
    *   section = "Operations",
    *   statusCodes={
    *       200="Returned when successful",
    *       404="Returned when the operation is not found"
    *   }
    * )
    *
    * @param Request $request
    * @param int $id
    */
    public function getOperationsAction(Request $request, $id)
    {
        $operationManager = $this->get('welp_batch.operation_manager');
        $operation = $operationManager->get($id);
        if ($operation == null) {
            throw new \Exception('Entity not found', 404);
        }
        return array(
            'success' => true,
            'operation' => $operation
        );
    }
}
