<?php

namespace spec\Welp\BatchBundle\Manager;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

use PhpSpec\ServiceContainer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Welp\BatchBundle\Manager\BatchManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Welp\BatchBundle\Model\Batch;
use Welp\BatchBundle\Model\Operation;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class BatchManagerSpec extends ObjectBehavior
{
    public function let(ObjectManager $em, ObjectRepository $repository)
    {
        $em->getRepository("Welp\BatchBundle\Model\Batch")->willReturn($repository);
        $this->beConstructedWith($em, "Welp\BatchBundle\Model\Batch", 'test/');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(BatchManager::class);
    }

    public function it_should_create_batch()
    {
        $this->createNew()->shouldBeAnInstanceOf(Batch::class);

        $this->createNew();
    }

    public function it_should_create_a_batch(ObjectManager $em)
    {
        $batch = new Batch();
        $em->persist($batch)->shouldBeCalled();
        $em->flush()->shouldBeCalled();
        $this->create($batch)->shouldReturn($batch);
        $this->create($batch)->shouldHaveType(Batch::class);

        $this->create($batch);
    }

    public function it_should_save_batch(ObjectManager $em)
    {
        $batch = new Batch();
        $em->persist($batch)->shouldBeCalled();
        $em->flush()->shouldBeCalled();
        $this->update($batch)->shouldReturn($batch);
        $this->update($batch)->shouldHaveType(Batch::class);

        $this->update($batch);
    }
    //TODO FInd a way to not create the file

    /*public function it_should_create_result_file_without_error(ServiceContainer $container, Filesystem $fs)
    {
        $batch = new Batch();
        $id = 1;
        $batch->setId($id);
        $batch->setOperations(array());

        $fs->exists('test')->shouldBeCalled()->willReturn(true);
        $fs->dumpFile('test/test', '{[]}')->willReturn(true);
        $fs->dumpFile('test/test', '{[]}')->shouldBeCalled();

        $this->generateResults($batch, 'test')->shouldBeString();

        $batch2 = new Batch();
        $id2 =2;
        $batch2->setId($id2);
        $batch2->setOperations(array(
            array(
                'operationId' =>1
            )
        ));
        $fs->dumpFile('test/', '[{"operationId":1,"error":false,"message":"operation OK"}]')->shouldBeCalled();
        $this->generateResults($batch2)->shouldBeString();
        $this->generateResults($batch2)->shouldContain('operation OK');
    }

    public function it_should_create_result_file_with_error(ServiceContainer $container)
    {
        $batch = new Batch();
        $id = 1;
        $batch->setId($id);
        $batch->setOperations(array(
            array(
                'operationId' =>$id
            )
        ));

        $batch->setErrors(array(
            array(
                'operationId' => $id,
                'error' => 'this is a test error'
            )
        ));

        $this->generateResults($batch)->shouldBeString();
        $this->generateResults($batch)->shouldContain('this is a test error');
    }*/
}
