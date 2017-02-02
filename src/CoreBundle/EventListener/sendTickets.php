<?php

namespace CoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use CoreBundle\Entity\Commande;

class sendTickets
{
    private $mailer;
    private $templating;
    private $snappy;

    public function __construct(\Swift_Mailer $mailer, $templating, $snappy)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->snappy = $snappy;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // On agit seulement si c'est une entité Commande
        if (!$entity instanceof Commande){
            return;
        }

        if (file_exists('bundles/core/pdf/billets.pdf')) {
            unlink('bundles/core/pdf/billets.pdf');
        }

        $this->snappy->generateFromHtml(
            $this->templating->render(
                'CoreBundle:Pdf:billets.html.twig',
                array('commande' => $entity)
            ),
            'bundles/core/pdf/billets.pdf'
        );

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
            )
            ->attach(\Swift_Attachment::fromPath('bundles/core/pdf/billets.pdf'));

        $this->mailer->send($message);
    }
}