<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class OrderControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(1, $crawler->filter('a:contains("Réserver mes billets maintenant")')->count());
    }

    public function testOrderInfo()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/orderInfo');

        $this->assertEquals(1, $crawler->filter('h2:contains("Informations sur la commande")')->count());

        // Sélection basée sur la valeur, l'id ou le nom des boutons
        $buttonCrawlerNode = $crawler->selectButton('Valider');
        $form = $buttonCrawlerNode->form();

        $form['corebundle_order[dateVisit]'] = '2017-03-03';
        $form['corebundle_order[ticketType]']->select('Journée');
        $form['corebundle_order[ticketsNb]'] = 3;
        $form['corebundle_order[mail][first]'] = 'nicolas.rizzon@gmail.com';
        $form['corebundle_order[mail][second]'] = 'nicolas.rizzon@gmail.com';

        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertEquals(1, $crawler->filter('h2:contains("Informations sur les billets")')->count());
    }

}