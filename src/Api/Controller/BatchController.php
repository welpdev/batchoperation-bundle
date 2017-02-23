<?php

namespace Welp\BatchBundle\Api\Controller;

use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;

use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class BatchController extends FOSRestController
{

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
    * @Rest\QueryParam(name="group", description="Name of the group of batches")
    *
    * @param Request $request
    * @param int $id
    */
    public function getBatchesAction(ParamFetcher $paramFetcher)
    {
        $params = $paramFetcher->all();

        $batchManager = $this->get('welp_batch.batch_manager');
        $batches = $batchManager->findBy($params);

        return array(
            'success' => true,
            'batch' => $batches
        );
    }

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
    public function getBatchAction(Request $request, $id)
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
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     */
    public function getBatchesResultsAction(Request $request, $id)
    {
        $batchManager = $this->get('welp_batch.batch_manager');

        $file = $batchManager->getResultFile($id);

        return array(
            'success' =>true,
            'file'=> $file
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
    *       {"name"="operations", "dataType"="array","required"=true, "description"="title of the category"},
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
        $group = $request->request->get('group');
        $batchManager = $this->get('welp_batch.batch_service');
        $batch = $batchManager->create($operations, $group);

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
    *   description = "delete a batch",
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
