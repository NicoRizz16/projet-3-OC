<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 24/01/2017
 * Time: 06:40
 */

namespace CoreBundle\Controller;

use CoreBundle\Entity\Billet;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Entity\Commande;
use CoreBundle\Form\CommandeType;
use CoreBundle\Form\CommandeBilletsType;


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
            // On ajoute le nombre de billets souhaité à la commande
            for($i=1; $i<=$commande->getNbBillets(); $i++){
                $billet = new Billet();
                $commande->addBillet($billet);
            }
            // On stocke les informations sur la commande dans une variable session
            $session = $request->getSession();
            $session->set('commande', $commande);

            // Puis on redirige vers l'étape suivante
            return $this->redirectToRoute('core_infosBillets');

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
            return $this->redirectToRoute('core_infosCommande');
        }

        $form = $this->get('form.factory')->create(CommandeBilletsType::class, $commande);

        // Si la requête est en POST c'est que le visiteur a soumis un formulaire
        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()){
            // On fait appel à un service pour calculer le tarif et le prix de chaque billet
            $commande = $this->get('core.setprice')->setTicketsPrice($commande);

            // On met à jour les informations sur la commande dans la session
            $session->set('commande', $commande);
            // On indique que la commande est prête pour l'étape 3
            $session->set('readyToPay', true);

            // Puis on redirige vers l'étape suivante
            return $this->redirectToRoute('core_paiement');
        }

        // Sinon on affiche la vue avec le formulaire
        return $this->render('CoreBundle:Order:infosBillets.html.twig', array(
            'form' => $form->createView(),
        ));

    }


    // Etape 3 de la commande : Récapitulatif et paiement.
    public function paiementAction(Request $request)
    {
        $session = $request->getSession();
        $commande = $session->get('commande');

        // Si la commande n'est pas prête pour le réglement, retour aux étapes précédentes
        if(!$session->get('readyToPay')){
            return $this->redirectToRoute('core_infosBillets');
        }

        // Sinon afficher le template de récapitulatif de la commande proposant le paiement
        return $this->render('CoreBundle:Order:paiement.html.twig', array(
            'commande' => $commande
        ));
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