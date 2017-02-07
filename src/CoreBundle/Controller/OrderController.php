<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 24/01/2017
 * Time: 06:40
 */

namespace CoreBundle\Controller;

use CoreBundle\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CoreBundle\Entity\Order;
use CoreBundle\Form\OrderType;
use CoreBundle\Form\OrderTicketsType;


class OrderController extends Controller
{
    // Page d'accueil
    public function indexAction()
    {
        return $this->render('CoreBundle:Order:index.html.twig');
    }


    // Etape 1 de la commande : Recueil des informations sur la commande.
    public function orderInfoAction(Request $request)
    {
        // On crée l'objet commande et son formulaire
        $order = new Order();
        $form = $this->get('form.factory')->create(OrderType::class, $order);

        // Si la requête est en POST c'est que le visiteur a soumis un formulaire
        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()){
            // On ajoute le nombre de billets souhaité à la commande
            for($i=1; $i<=$order->getTicketsNb(); $i++){
                $ticket = new Ticket();
                $order->addTicket($ticket);
            }
            // On stocke les informations sur la commande dans une variable session
            $session = $request->getSession();
            $session->set('order', $order);

            // Puis on redirige vers l'étape suivante
            return $this->redirectToRoute('core_ticketsInfo');

        }

        // Sinon on affiche la vue avec le formulaire
        return $this->render('CoreBundle:Order:orderInfo.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    // Etape 2 de la commande : Recueil des informations sur les billets.
    public function ticketsInfoAction(Request $request)
    {
        $session = $request->getSession();
        $order = $session->get('order');
        // Si on a pas d'informations sur la commande dans la session on retourne à l'étape 1.
        if(!isset($order)){
            return $this->redirectToRoute('core_orderInfo');
        }

        $form = $this->get('form.factory')->create(OrderTicketsType::class, $order);

        // Si la requête est en POST c'est que le visiteur a soumis un formulaire
        if($request->isMethod('POST') && $form->handleRequest($request)->isValid()){
            // On fait appel à un service pour calculer le tarif et le prix de chaque billet
            $order = $this->get('core.setprice')->setTicketsPrice($order);

            // On met à jour les informations sur la commande dans la session
            $session->set('order', $order);
            // On indique que la commande est prête pour l'étape 3
            $session->set('readyToPay', true);

            // Puis on redirige vers l'étape suivante
            return $this->redirectToRoute('core_payment');
        }

        // Sinon on affiche la vue avec le formulaire
        // Récupération des tarifs pour les passer à la vue
        $normal_fare = $this->container->getParameter('normal_fare');
        $senior_fare = $this->container->getParameter('senior_fare');
        $reduced_fare = $this->container->getParameter('reduced_fare');
        $child_fare = $this->container->getParameter('child_fare');

        return $this->render('CoreBundle:Order:ticketsInfo.html.twig', array(
            'form' => $form->createView(),
            'normal_fare' => $normal_fare,
            'senior_fare' => $senior_fare,
            'reduced_fare' => $reduced_fare,
            'child_fare' => $child_fare,
            'ticketsNb' => $order->getTicketsNb(),
            'ticketType' => $order->getTicketType()
        ));
    }


    // Etape 3 de la commande : Récapitulatif et paiement.
    public function paymentAction(Request $request)
    {
        $session = $request->getSession();
        $order = $session->get('order');

        // Si la commande n'est pas prête pour le réglement, retour aux étapes précédentes
        if(!$session->get('readyToPay')){
            return $this->redirectToRoute('core_ticketsInfo');
        }

        // Sinon afficher le template de récapitulatif de la commande proposant le paiement
        return $this->render('CoreBundle:Order:payment.html.twig', array(
            'order' => $order,
            'key' => $this->container->getParameter('stripe_public_key')
        ));
    }


    // Etape 4 de la commande : Confirmation et traitement de la commande
    public function confirmAction(Request $request)
    {
        // clé secrète Stripe
        \Stripe\Stripe::setApiKey($this->container->getParameter('stripe_private_key'));

        $session = $request->getSession();
        $order = $session->get('order');
        // Récupération du token généré par le formulaire checkout
        $token = $request->get('stripeToken');

        if(!$session->get('readyToPay') | !isset($token)){
            return $this->redirectToRoute('core_payment');
        }

        try { // On procède au paiement
            \Stripe\Charge::create(array(
                "amount" => $order->getTotalPrice()*100,
                "currency" => "eur",
                "description" => "Example charge",
                "source" => $token,
            ));
        } catch(\Stripe\Error\Card $e) { // Gestion des erreurs concernant les informations de paiement
            $body = $e->getJsonBody();
            $err = $body['error'];
            $request->getSession()->getFlashBag()->add('error', $err['message']);
            return $this->redirectToRoute('core_error', array('retry' => "true"));
        } catch (\Stripe\Error\Base $e) { // Gestion globale des erreurs
            $request->getSession()->getFlashBag()->add('error', 'Nous avons rencontré un problème lors de la procédure du paiement, veuillez réessayer.');
            return $this->redirectToRoute('core_error', array('retry' => "true"));
        }

        // Génération d'un code unique pour chaque billet de la commande
        foreach ($order->getTickets() as $ticket){
            $ticket->setCode(uniqid());
        }

        try {
            // On enregistre la commande
            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();
        } catch (\Exception $e){
            $request->getSession()->getFlashBag()->add('error', 'Un problème est survenu lors de l\'enregistrement de votre commande.
            Veuillez contacter notre service client au plus vite pour obtenir vos billets ou un remboursement.
            Nous vous prions d\'accepter toutes nos excuses pour ce dérangement.');
            return $this->redirectToRoute('core_error');
        }

        // On réinitialise la session
        $session->set('order', null);
        $session->set('readyToPay', false);

        return $this->render('CoreBundle:Order:confirm.html.twig');
    }


    // Erreur de paiement
    public function errorAction($retry)
    {
        return $this->render('CoreBundle:Order:error.html.twig', array('retry' => $retry));
    }

}