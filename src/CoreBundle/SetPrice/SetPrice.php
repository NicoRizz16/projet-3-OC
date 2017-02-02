<?php

namespace CoreBundle\SetPrice;

use CoreBundle\Entity\Commande;
use CoreBundle\Entity\Billet;
use Symfony\Component\Validator\Constraints\DateTime;

class SetPrice
{
    private $tarif_normal;
    private $tarif_senior;
    private $tarif_reduit;
    private $tarif_enfant;

    public function __construct($tarif_normal, $tarif_senior, $tarif_reduit, $tarif_enfant)
    {
        $this->tarif_normal = $tarif_normal;
        $this->tarif_senior = $tarif_senior;
        $this->tarif_reduit = $tarif_reduit;
        $this->tarif_enfant = $tarif_enfant;
    }

    public function setTicketsPrice(Commande $commande)
    {
        $prixTotal = 0;
        // Traitement de tous les billets de la commande un par un
        foreach ($commande->getBillets() as $billet){
            if($billet->getTarifReduit()){  // Si le billet bénéficie d'un tarif réduit
                $billet->setTarif("Tarif réduit");
                $billet->setPrix($this->tarif_reduit);
            } else { // Sinon on calcule le tarif en fonction de l'âge
                $billet = $this->setTicketPriceByAge($billet);
            }
            // Si les billets sont de type demi-journée, on divise le tarif du billet par 2.
            if($commande->getTypeBillet()=="Demi-journée"){
                $billet->setPrix($billet->getPrix()/2);
            }

            // On ajoute le prix du billet au prix total
            $prixTotal += $billet->getPrix();
        }
        $commande->setPrixTotal($prixTotal);
        return $commande;
    }

    public function setTicketPriceByAge(Billet $billet)
    {
        $age = $this->getAge($billet->getDateNaissance());
        if($age < 4){
            $billet->setTarif("Gratuit");
            $billet->setPrix(0);
        } elseif ($age < 12){
            $billet->setTarif("Tarif enfant");
            $billet->setPrix($this->tarif_enfant);
        } elseif ($age < 60){
            $billet->setTarif("Tarif normal");
            $billet->setPrix($this->tarif_normal);
        } else {
            $billet->setTarif("Tarif senior");
            $billet->setPrix($this->tarif_senior);
        }

        return $billet;
    }

    public function getAge(\DateTime $dateNaissance)
    {
        $dateDuJour = new \DateTime();
        $interval = $dateNaissance->diff($dateDuJour);
        return $interval->format('%y');
    }

}