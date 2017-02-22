<?php

namespace Welp\BatchBundle\Manager;

use JsonSerializable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Welp\BatchBundle\Model\Batch;
use Welp\BatchBundle\Manager\ManagerInterface as BaseManager;
use Welp\BatchBundle\Model\BatchInterface;

/**
 * batch manager
 */
class BatchManager implements BaseManager
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * @var String
     */
    private $class;

    /**
     *
     * @param String $entityManager name of the entitytyManager service
     * @param ContainerInterface $container
     * @param String $className     Name of the class that extends our batchModel
     */
    public function __construct($entityManager, $container, $className)
    {
        $this->container = $container;
        $this->entityManager = $this->container->get($entityManager);
        $this->repository = $this->entityManager->getRepository($className);
        //$metadata = $this->entityManager->getClassMetadata($className);
        $this->class = $className;
    }

    /**
     * {@inheritdoc}
     */
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

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($entity)
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }

    /**
     * This function get all result of operations, and store them in a file. This file is located in folder give in the configuration. The file will be name like this : results-{batchId}-{dateToday}
     * @param  BatchInterface $batch
     * @return JsonSerializable
     */
    public function generateResults($batch)
    {
        $today = new \DateTime();
        $fileName = $this->container->getParam('welp_batch.batch_results_folder').'results-'.$batch->getId().'-'.$today->format('Y-m-d\TH-i-s');
        $arrayResponses = array();

        foreach ($batch->getOperations() as $operation) {
            $test = array_filter($batch->getErrors(), function ($e) use ($operation) {
                return $e['operationId'] == $operation['operationId'];
            });
            $result = array();
            $result['operationId'] = $operation['operationId'];
            $result['error'] = false;
            if (count($test)>0) {
                $result['text'] = $test[0]["error"];
                $result['error'] = true;
            } else {
                $result['message'] = 'operation OK';
            }

            $arrayResponses[] = $result;
        }

        file_put_contents($fileName, json_encode($arrayResponses));

        return json_encode($arrayResponses);
    }

    /**
     * This function return the result file for the batch with the given id.
     * @param  integer $id
     * @return JsonSerializable
     */
    public function getResultFile($id)
    {
        $batch = $this->repository->findOneById($id);
        $filenameGlob = $fileName = $this->container->getParam('welp_batch.batch_results_folder').'results-'.$batch->getId().'-*';

        $files = glob($filenameGlob);
        $content = "";
        if (count($files) > 0) {
            $content = file_get_contents(end($files));
        } else {
            $content = $this->generateResults($batch); // the files don't exist, we regenerate it.
        }
        $content = $content;
        return $content;
    }
}
