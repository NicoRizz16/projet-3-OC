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

    public function testInfosCommande()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/infosCommande');

        $this->assertEquals(1, $crawler->filter('h2:contains("Informations sur la commande")')->count());

        // Sélection basée sur la valeur, l'id ou le nom des boutons
        $buttonCrawlerNode = $crawler->selectButton('Valider');
        $form = $buttonCrawlerNode->form();

        $form['corebundle_commande[dateVisite]'] = '2017-03-03';
        $form['corebundle_commande[typeBillet]']->select('Journée');
        $form['corebundle_commande[nbBillets]'] = 3;
        $form['corebundle_commande[mail][first]'] = 'nicolas.rizzon@gmail.com';
        $form['corebundle_commande[mail][second]'] = 'nicolas.rizzon@gmail.com';

        $crawler = $client->submit($form);
        $crawler = $client->followRedirect();

        $this->assertEquals(1, $crawler->filter('h2:contains("Informations sur les billets")')->count());
    }

}