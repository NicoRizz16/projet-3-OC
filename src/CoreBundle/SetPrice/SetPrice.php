<?php

namespace CoreBundle\SetPrice;

use CoreBundle\Entity\Order;
use CoreBundle\Entity\Ticket;
use Symfony\Component\Validator\Constraints\DateTime;

class SetPrice
{
    private $normal_fare;
    private $senior_fare;
    private $reduced_fare;
    private $child_fare;

    public function __construct($normal_fare, $senior_fare, $reduced_fare, $child_fare)
    {
        $this->normal_fare = $normal_fare;
        $this->senior_fare = $senior_fare;
        $this->reduced_fare = $reduced_fare;
        $this->child_fare = $child_fare;
    }

    public function setTicketsPrice(Order $order)
    {
        $totalPrice = 0;
        // Traitement de tous les tickets de la commande un par un
        foreach ($order->getTickets() as $ticket){
            if($ticket->getReducedFare()){  // Si le ticket bénéficie d'un tarif réduit
                $ticket->setFare("Tarif réduit");
                $ticket->setPrice($this->reduced_fare);
            } else { // Sinon on calcule le tarif en fonction de l'âge
                $ticket = $this->setTicketPriceByAge($ticket);
            }
            // Si les tickets sont de type demi-journée, on divise le tarif du ticket par 2.
            if($order->getTicketType()=="Demi-journée"){
                $ticket->setPrice($ticket->getPrice()/2);
            }

            // On ajoute le prix du ticket au prix total
            $totalPrice += $ticket->getPrice();
        }
        $order->setTotalPrice($totalPrice);
        return $order;
    }

    public function setTicketPriceByAge(ticket $ticket)
    {
        $age = $this->getAge($ticket->getDateOfBirth());
        if($age < 4){
            $ticket->setFare("Gratuit");
            $ticket->setPrice(0);
        } elseif ($age < 12){
            $ticket->setFare("Tarif enfant");
            $ticket->setPrice($this->child_fare);
        } elseif ($age < 60){
            $ticket->setFare("Tarif normal");
            $ticket->setPrice($this->normal_fare);
        } else {
            $ticket->setFare("Tarif senior");
            $ticket->setPrice($this->senior_fare);
        }

        return $ticket;
    }

    public function getAge(\DateTime $dateOfBirth)
    {
        $now = new \DateTime();
        $interval = $dateOfBirth->diff($now);
        return $interval->format('%y');
    }

}