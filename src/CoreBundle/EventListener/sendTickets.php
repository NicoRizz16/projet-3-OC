<?php

namespace CoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use CoreBundle\Entity\Commande;

class sendTickets
{
    private $mailer;
    private $templating;

    public function __construct(\Swift_Mailer $mailer, $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // On agit seulement si c'est une entité Commande
        if (!$entity instanceof Commande){
            return;
        }

        $message = \Swift_Message::newInstance()
            ->setSubject('Confirmation de votre réservation')
            ->setFrom('billeterie-du-louvre@projet3.nicolasrizzon.fr')
            ->setTo($entity->getMail())
            ->setBody(
                $this->templating->render(
                    'CoreBundle:Emails:billets.html.twig',
                    array('commande' => $entity)
                ),
                'text/html'
            );

        $this->mailer->send($message);
    }
}