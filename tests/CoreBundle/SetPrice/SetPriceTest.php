<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SetPriceTest extends WebTestCase
{

    private $setPrice;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->setPrice = $kernel->getContainer()->get('core.setprice');
    }

    public function testSetTicketPriceByAge()
    {
        $normalTicket = new \CoreBundle\Entity\Ticket();
        $normalTicket->setDateOfBirth($this->getTime(-30*365*24*3600));
        $normalTicket = $this->setPrice->setTicketPriceByAge($normalTicket);
        $childTicket = new \CoreBundle\Entity\Ticket();
        $childTicket->setDateOfBirth($this->getTime(-8*365*24*3600));
        $childTicket = $this->setPrice->setTicketPriceByAge($childTicket);
        $seniorTicket = new \CoreBundle\Entity\Ticket();
        $seniorTicket->setDateOfBirth($this->getTime(-70*365*24*3600));
        $seniorTicket = $this->setPrice->setTicketPriceByAge($seniorTicket);

        $this->assertTrue("Tarif normal" === $normalTicket->getFare());
        $this->assertTrue("Tarif enfant" === $childTicket->getFare());
        $this->assertTrue("Tarif senior" === $seniorTicket->getFare());
    }

    public function getTime($delta)
    {
        $date = new \DateTime();
        return $date->setTimestamp(time()+$delta);
    }
}