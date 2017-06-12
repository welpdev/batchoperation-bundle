<?php

namespace Welp\BatchBundle\Manager;

use JsonSerializable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Welp\BatchBundle\Model\Batch;
use Welp\BatchBundle\Manager\ManagerInterface as BaseManager;
use Welp\BatchBundle\Model\BatchInterface;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

use Doctrine\DBAL\LockMode;

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
     * @var EntityRepository
     */
    private $repository;

    /**
     * @var String
     */
    private $class;

    /**
     *
     * @var String
     */
    private $folderName;

    /**
     *
     * @param EntityManager $entityManager name of the entitytyManager service
     * @param String $className     Name of the class that extends our batchModel
     * @param String $folderName Name of the folder when results file will be created
     */
    public function __construct($entityManager, $className, $folderName)
    {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository($className);
        $this->folderName = $folderName;
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

    public function findBy(array $params)
    {
        //check for pagination
        if (!isset($params['offset'])) {
            $params['offset'] =1;
        }

        if (!isset($params['limit'])) {
            $params['limit'] =10;
        }
        $offset = $params['offset'];
        $limit = $params['limit'];

        unset($params['offset']);
        unset($params['limit']);

        if (key_exists('group', $params) && $params['group'] != null) {
            $batches = $this->repository->findBy($params, null, $limit, $offset);
        } else {
            $batches = $this->repository->findBy(array(), null, $limit, $offset);
        }


        return $batches;
    }

    /**
     * {@inheritdoc}
     */
    public function update($entity)
    {
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $test = $this->entityManager->find($this->class, $entity->getId(), LockMode::PESSIMISTIC_WRITE);
            $test->setUpdatedAt(new \DateTime());
            $this->entityManager->persist($test);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
            return $test;
        } catch (\Exception $ex) {
            $this->entityManager->getConnection()->rollback();
            return $this->update($entity);
        }
    }

    public function addExecutedOperation($batch, $message)
    {
        $this->entityManager->getConnection()->beginTransaction();
        try {
            $test = $this->entityManager->find($this->class, $batch->getId(), LockMode::PESSIMISTIC_WRITE);
            $test->addExecutedOperations($message);
            $this->entityManager->persist($test);
            $this->entityManager->flush();
            $this->entityManager->getConnection()->commit();
            return $test;
        } catch (\Exception $ex) {
            $this->entityManager->getConnection()->rollback();
            return $this->addExecutedOperation($batch, $message);
        }
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
    public function generateResults($batch, $fileName='')
    {
        $today = new \DateTime();

        if ($fileName == '') {
            $fileName = $this->folderName.'results-'.$batch->getId().'-'.$today->format('Y-m-d\TH-i-s');
        } else {
            $fileName= $this->folderName.$fileName;
        }

        $arrayResponses = array();

        foreach ($batch->getOperations() as $operation) {
            $test = array_filter($batch->getErrors(), function ($e) use ($operation) {
                return $e['operationId'] == $operation['operationId'];
            });

            $result = array();
            $result['operationId'] = $operation['operationId'];
            $result['error'] = false;

            if (count($test)>0) {
                $result['text'] = reset($test)["error"];
                $result['error'] = true;
            } else {
                $executedOperation = array_filter($batch->getExecutedOperations(), function ($e) use ($operation) {
                    return $e['operationId'] == $operation['operationId'];
                });

                $result['message'] = reset($executedOperation)['message'];
            }

            $arrayResponses[] = $result;
        }
        $content = json_encode($arrayResponses);
        $fs = new Filesystem();

        if (!$fs->exists($this->folderName)) {
            $fs->mkDir($this->folderName);
        }

        $fs->dumpFile($fileName, $content);
        return $fileName;
    }

    /**
     * This function return the result file for the batch with the given id.
     * @param  integer $id
     * @return JsonSerializable
     */
    public function getResultFile($id)
    {
        $batch = $this->repository->findOneById($id);

        $finder = new Finder();

        $filenameTemp = $this->folderName.'results-'.$batch->getId().'-*';

        $finder->files()->name('results-'.$batch->getId().'-*');
        $files = iterator_to_array($finder->in($this->folderName));

        $content = "";

        if (count($files)> 0) {
            $file = end($files);
            $fileName = $file->getRealPath();
        } else {
            $fileName = $this->generateResults($batch);
        }

        $fileName = $fileName;
        return $fileName;
    }

    public function formatMessage($operationId, $consumerMessage=null)
    {
        if ($consumerMessage == null) {
            return array('operationId' => $operationId);
        }

        $message = array('operationId' => $operationId, 'message' => $consumerMessage);
        return $message;
    }
}
