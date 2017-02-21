<?php

namespace Welp\BatchBundle\Manager;

use Welp\BatchBundle\Model\Batch;
use Welp\BatchBundle\Manager\ManagerInterface as BaseManager;

/**
 * batch Factory
 */
class BatchManager implements BaseManager
{
    private $entityManager;
    private $container;
    private $repository;
    private $class;

    public function __construct($entityManager, $container, $className)
    {
        $this->container = $container;
        $this->entityManager = $this->container->get($entityManager);
        $this->repository = $this->entityManager->getRepository($className);
        //$metadata = $this->entityManager->getClassMetadata($className);
        $this->class = $className;
    }

    public function createNew()
    {
        $batch = new $this->class();
        return $batch;
    }

    /**
     * {@inheritdoc}
     */
    public function create($entity)
    {
        //handle timestamp
        $entity->setCreatedAt(new \DateTime());
        $entity->setUpdatedAt(new \DateTime());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        $batch = $this->repository->findOneById($id);

        return $batch;
    }

    /**
     * {@inheritdoc}
     */
    public function update($entity)
    {
        $entity->setUpdatedAt(new \DateTime());
        //dump($entity);die();
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function delete($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    public function generateResults($batch){
        $today = new \DateTime();
        $fileName = $this->container->getParameter('welp_batch.batch_results_folder').'results-'.$batch->getId().'-'.$today->format('Y-m-d\TH-i-s');
        $arrayResponses = array();

        foreach ($batch->getOperations() as $operation) {
            $test = array_filter($batch->getErrors(), function($e) use ($operation){
                return $e['operationId'] == $operation['operationId'];
            });
            $result = array();
            $result['operationId'] = $operation['operationId'];
            $result['error'] = false;
            if(count($test)>0){
                $result['text'] = $test[0]["error"];
                $result['error'] = true;
            }else{
                $result['message'] = 'operation OK';
            }

            $arrayResponses[] = $result;
        }

        file_put_contents($fileName,json_encode($arrayResponses));

        return json_encode($arrayResponses);
    }

    public function getResultFile($id){
        $batch = $this->repository->findOneById($id);
        $filenameGlob = $fileName = $this->container->getParameter('welp_batch.batch_results_folder').'results-'.$batch->getId().'-*';

        $files = glob($filenameGlob);
        $content = "";
        if(count($files) > 0){
            $content = file_get_contents(end($files));
        }else{
            $content = $this->generateResults($batch); // the files don't exist, we regenerate it.
        }
        $content = $content;
        return $content;
    }
}
