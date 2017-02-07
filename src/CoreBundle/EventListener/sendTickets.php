<?php

namespace CoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use CoreBundle\Entity\Order;

class sendTickets
{
    private $mailer;
    private $templating;
    private $snappy;
    private $mailer_user;

    public function __construct(\Swift_Mailer $mailer, $templating, $snappy, $mailer_user)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->snappy = $snappy;
        $this->mailer_user = $mailer_user;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        // On agit seulement si c'est une entité Commande
        if (!$entity instanceof Order){
            return;
        }

        if (file_exists('bundles/core/pdf/tickets.pdf')) {
            unlink('bundles/core/pdf/tickets.pdf');
        }

        $this->snappy->generateFromHtml(
            $this->templating->render(
                'CoreBundle:Pdf:tickets.html.twig',
                array('order' => $entity)
            ),
            'bundles/core/pdf/tickets.pdf'
        );

        $message = \Swift_Message::newInstance()
            ->setSubject('Confirmation de votre réservation')
            ->setFrom($this->mailer_user)
            ->setTo($entity->getMail())
            ->setBody(
                $this->templating->render(
                    'CoreBundle:Emails:tickets.html.twig',
                    array('order' => $entity)
                ),
                'text/html'
            )
            ->attach(\Swift_Attachment::fromPath('bundles/core/pdf/tickets.pdf'));

        $this->mailer->send($message);
    }
}