<?php

// tests/CoreBundle/Repository/OrderRepositoryTest.php
namespace tests\CoreBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Constraints\DateTime;

class OrderRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testSumTicketsSold()
    {
        $nbTickets = $this->em
            ->getRepository('CoreBundle:Order')
            ->sumTicketsSold(new \DateTime('2016-03-03'))
        ;
        $this->assertEquals(0, $nbTickets);

        $nbTickets = $this->em
            ->getRepository('CoreBundle:Order')
            ->sumTicketsSold(new \DateTime('2018-03-03'))
        ;
        $this->assertEquals(0, $nbTickets);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
}