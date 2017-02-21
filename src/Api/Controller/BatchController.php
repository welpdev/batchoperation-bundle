<?php

namespace Welp\BatchBundle\Api\Controller;

use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class BatchController extends FOSRestController
{
    //TODO api_doc

    /**
    *
    * @Rest\View(serializerEnableMaxDepthChecks=true)
    * @ApiDoc(
    *   resource = true,
    *   description = "Return a single batch with the given id",
    *   section = "Batches",
    *   statusCodes={
    *       200="Returned when successful",
    *       404="Returned when the batch is not found"
    *   }
    * )
    *
    * @param Request $request
    * @param int $id
    */
    public function getBatchesAction(Request $request, $id)
    {
        $batchManager = $this->get('welp_batch.batch_manager');
        $batch = $batchManager->get($id);
        if ($batch == null) {
            throw new \Exception('Entity not found', 404);
        }
        return array(
            'success' => true,
            'batch' => $batch
        );
    }

    /**
    *
    * @Rest\View(serializerEnableMaxDepthChecks=true)
    * @ApiDoc(
    *   resource = true,
    *   description = "Create a batch",
    *   section = "Batches",
    *   parameters ={
    *   },
    *   statusCodes={
    *       200="Returned when successful",
    *       404="Returned when the batch is not found"
    *   }
    * )
    *
    * @param Request $request
    */
    public function postBatchesAction(Request $request)
    {
        $operations = $request->request->get('operations');
        $batchManager = $this->get('welp_batch.batch_service');
        $batch = $batchManager->create($operations);

        return array(
            'success' => true,
            'batch' => $batch
        );
    }

    /**
    *
    * @Rest\View(serializerEnableMaxDepthChecks=true)
    * @ApiDoc(
    *   resource = true,
    *   section = "Batches",
    *   parameters ={
    *       {"name"="id", "dataType"="integer","required"=true, "description"="id of the batch"},
    *   },
    *   statusCodes={
    *       200="Returned when successful",
    *       404="Returned when the batch is not found"
    *   }
    * )
    *
    * @param Request $request
    * @param int $id
    */
    public function deleteBatchesAction(Request $request, $id)
    {
        $batchManager = $this->get('welp_batch.batch_manager');
        $batch = $batchManager->get($id);
        if ($batch == null) {
            throw new \Exception('Entity not found', 404);
        }
        $batchManager->delete($batch);

        return array(
            'success' => true,
            'message' => 'batch deleted'
        );
    }
}
