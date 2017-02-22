<?php

namespace spec\Welp\BatchBundle\Service;

use Welp\BatchBundle\Service\BatchService;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;

class BatchServiceSpec extends ObjectBehavior
{
    /*public function let(ServiceContainer $container, EntityManager $em)
    {
        $container->get("doctrine.orm.entity_manager")->willReturn($em);
        $this->beConstructedWith("doctrine.orm.entity_manager", $container, "Welp\BatchBundle\Model\Batch");
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(BatchManager::class);
    }*/
}
