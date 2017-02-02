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
        $billetNormal = new \CoreBundle\Entity\Billet();
        $billetNormal->setDateNaissance($this->getTime(-30*365*24*3600));
        $billetNormal = $this->setPrice->setTicketPriceByAge($billetNormal);
        $billetEnfant = new \CoreBundle\Entity\Billet();
        $billetEnfant->setDateNaissance($this->getTime(-8*365*24*3600));
        $billetEnfant = $this->setPrice->setTicketPriceByAge($billetEnfant);
        $billetSenior = new \CoreBundle\Entity\Billet();
        $billetSenior->setDateNaissance($this->getTime(-70*365*24*3600));
        $billetSenior = $this->setPrice->setTicketPriceByAge($billetSenior);

        $this->assertTrue("Tarif normal" === $billetNormal->getTarif());
        $this->assertTrue("Tarif enfant" === $billetEnfant->getTarif());
        $this->assertTrue("Tarif senior" === $billetSenior->getTarif());
    }

    public function getTime($delta)
    {
        $date = new \DateTime();
        return $date->setTimestamp(time()+$delta);
    }
}