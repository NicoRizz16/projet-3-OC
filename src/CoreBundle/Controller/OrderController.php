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
        // Récupération des tarifs pour les passer à la vue
        $tarifNormal = $this->container->getParameter('tarif_normal');
        $tarifSenior = $this->container->getParameter('tarif_senior');
        $tarifReduit = $this->container->getParameter('tarif_reduit');
        $tarifEnfant = $this->container->getParameter('tarif_enfant');

        return $this->render('CoreBundle:Order:infosBillets.html.twig', array(
            'form' => $form->createView(),
            'tarifNormal' => $tarifNormal,
            'tarifSenior' => $tarifSenior,
            'tarifReduit' => $tarifReduit,
            'tarifEnfant' => $tarifEnfant,
            'nbBillets' => $commande->getNbBillets()
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
            'commande' => $commande,
            'key' => $this->container->getParameter('stripe_public_key')
        ));
    }


    // Etape 4 de la commande : Confirmation et traitement de la commande
    public function confirmAction(Request $request)
    {
        // clé secrète Stripe
        \Stripe\Stripe::setApiKey($this->container->getParameter('stripe_private_key'));

        $session = $request->getSession();
        $commande = $session->get('commande');
        // Récupération du token généré par le formulaire checkout
        $token = $request->get('stripeToken');

        if(!$session->get('readyToPay') | !isset($token)){
            return $this->redirectToRoute('core_paiement');
        }

        try { // On procède au paiement
            \Stripe\Charge::create(array(
                "amount" => $commande->getPrixTotal()*100,
                "currency" => "eur",
                "description" => "Example charge",
                "source" => $token,
            ));
        } catch(\Stripe\Error\Card $e) { // Gestion des erreurs concernant les informations de paiement
            $body = $e->getJsonBody();
            $err = $body['error'];
            $request->getSession()->getFlashBag()->add('erreur', $err['message']);
            return $this->redirectToRoute('core_erreur');
        } catch (\Stripe\Error\Base $e) { // Gestion globale des erreurs
            $request->getSession()->getFlashBag()->add('erreur', 'Nous avons rencontré un problème lors de la procédure du paiement, veuillez réessayer.');
            return $this->redirectToRoute('core_erreur');
        }

        // Génération d'un code unique pour chaque billet de la commande
        foreach ($commande->getBillets() as $billet){
            $billet->setCode(uniqid());
        }

        // On enregistre la commande
        $em = $this->getDoctrine()->getManager();
        $em->persist($commande);
        $em->flush();

        // On réinitialise la session
        $session->set('commande', null);
        $session->set('readyToPay', false);

        return $this->render('CoreBundle:Order:confirm.html.twig');
    }


    // Erreur de paiement
    public function erreurAction()
    {
        return $this->render('CoreBundle:Order:erreur.html.twig');
    }
}