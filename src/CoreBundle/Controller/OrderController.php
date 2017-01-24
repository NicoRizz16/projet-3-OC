<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 24/01/2017
 * Time: 06:40
 */

namespace CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Entity\Commande;
use CoreBundle\Form\CommandeType;


class OrderController extends Controller
{
    // Page d'accueil
    public function indexAction()
    {
        return $this->render('CoreBundle:Order:index.html.twig');
    }


    // Etape 1 de la commande : Recueil des informations sur la commande.
    public function infosCommandeAction(Request $request)
    {
        // On crée l'objet commande et son formulaire
        $commande = new Commande();
        $form = $this->get('form.factory')->create(CommandeType::class, $commande);

        // Si la requête est en POST c'est que le visiteur a soumis un formulaire
        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()){

            // On stocke les informations sur la commande dans une variable session
            $session = $request->getSession();
            $session->set('commande', $commande);

            // Puis on redirige vers l'étape suivante
            return $this->redirectToRoute('core_infosBillet');

        }

        // Sinon on affiche la vue avec le formulaire
        return $this->render('CoreBundle:Order:infosCommande.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    // Etape 2 de la commande : Recueil des informations sur les billets.
    public function infosBilletsAction(Request $request)
    {
        $session = $request->getSession();
        $commande = $session->get('commande');
        // Si on a pas d'informations sur la commande dans la session on retourne à l'étape 1.
        if(!isset($commande)){
            return $this->redirectToRoute('core_homepage');
        }

    }


    // Etape 3 de la commande : Récapitulatif et paiement.
    public function paiementAction()
    {

    }


    // Etape 4 de la commande : Confirmation et traitement de la commande
    public function confirmAction()
    {

    }


    // Erreur de paiement
    public function erreurAction()
    {

    }
}