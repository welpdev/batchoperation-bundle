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

class BatchManagerSpec extends ObjectBehavior
{
    public function let(ServiceContainer $container, ObjectManager $em, ObjectRepository $repository)
    {
        $container->get("doctrine.orm.entity_manager")->willReturn($em);
        $em->getRepository("Welp\BatchBundle\Model\Batch")->willReturn($repository);
        $this->beConstructedWith("doctrine.orm.entity_manager", $container, "Welp\BatchBundle\Model\Batch");
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

    public function it_should_create_result_file_without_error(ServiceContainer $container)
    {
        $batch = new Batch();
        $id = 1;
        $batch->setId($id);
        $container->getParam("welp_batch.batch_results_folder")->willReturn('.');
        $batch->setOperations(array());

        $this->generateResults($batch)->shouldBeString();

        $batch2 = new Batch();
        $id2 =2;
        $batch2->setId($id2);
        $batch2->setOperations(array(
            array(
                'operationId' =>1
            )
        ));

        $this->generateResults($batch2)->shouldBeString();
        $this->generateResults($batch2)->shouldContain('operation OK');
    }

    public function it_should_create_result_file_with_error(ServiceContainer $container)
    {
        $batch = new Batch();
        $id = 1;
        $batch->setId($id);
        $container->getParam("welp_batch.batch_results_folder")->willReturn('.');
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
    }
}
