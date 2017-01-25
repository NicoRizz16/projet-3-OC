<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use CoreBundle\Validator\ReservationOpen;

/**
 * Commande
 *
 * @ORM\Table(name="commande")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\CommandeRepository")
 */
class Commande
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCommande", type="datetime")
     */
    private $dateCommande;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=255)
     * @Assert\Email(
     *     message = "L'adresse mail entrée n'est pas valide.",
     *     checkMX = true
 *     )
     */
    private $mail;

    /**
     * @ORM\OneToMany(targetEntity="CoreBundle\Entity\Billet", mappedBy="commande", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid
     */
    private $billets;

    /**
     * @var int
     *
     * @ORM\Column(name="nbBillets", type="smallint")
     * @Assert\Type(
     *     type="integer",
     *     message="La valeur entrée n'est pas un type valide."
     * )
     * @Assert\Range(
     *      min = 1,
     *      max = 30,
     *      minMessage = "Vous devez commander au moins un billet.",
     *      maxMessage = "Vous ne pouvez pas commander plus de 30 billets d'un coup."
     * )
     */
    private $nbBillets;

    /**
     * @var string
     *
     * @ORM\Column(name="typeBillet", type="string", length=255)
     * @Assert\Choice({"Journée", "Demi-journée"})
     */
    private $typeBillet;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateVisite", type="datetime")
     * @Assert\Date()
     * @Assert\GreaterThanOrEqual("today")
     * @ReservationOpen()
     */
    private $dateVisite;

    /**
     * @var int
     * @ORM\Column(name="prixTotal", type="integer")
     */
    private $prixTotal;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->dateCommande = new \Datetime();
        $this->billets = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dateCommande
     *
     * @param \DateTime $dateCommande
     *
     * @return Commande
     */
    public function setDateCommande($dateCommande)
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    /**
     * Get dateCommande
     *
     * @return \DateTime
     */
    public function getDateCommande()
    {
        return $this->dateCommande;
    }

    /**
     * Set mail
     *
     * @param string $mail
     *
     * @return Commande
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set nbBillets
     *
     * @param integer $nbBillets
     *
     * @return Commande
     */
    public function setNbBillets($nbBillets)
    {
        $this->nbBillets = $nbBillets;

        return $this;
    }

    /**
     * Get nbBillets
     *
     * @return int
     */
    public function getNbBillets()
    {
        return $this->nbBillets;
    }

    /**
     * Set typeBillet
     *
     * @param string $typeBillet
     *
     * @return Commande
     */
    public function setTypeBillet($typeBillet)
    {
        $this->typeBillet = $typeBillet;

        return $this;
    }

    /**
     * Get typeBillet
     *
     * @return string
     */
    public function getTypeBillet()
    {
        return $this->typeBillet;
    }

    /**
     * Set dateVisite
     *
     * @param \DateTime $dateVisite
     *
     * @return Commande
     */
    public function setDateVisite($dateVisite)
    {
        $this->dateVisite = $dateVisite;

        return $this;
    }

    /**
     * Get dateVisite
     *
     * @return \DateTime
     */
    public function getDateVisite()
    {
        return $this->dateVisite;
    }

    /**
     * Add billet
     *
     * @param \CoreBundle\Entity\Billet $billet
     *
     * @return Commande
     */
    public function addBillet(\CoreBundle\Entity\Billet $billet)
    {
        $this->billets[] = $billet;

        return $this;
    }

    /**
     * Remove billet
     *
     * @param \CoreBundle\Entity\Billet $billet
     */
    public function removeBillet(\CoreBundle\Entity\Billet $billet)
    {
        $this->billets->removeElement($billet);
    }

    /**
     * Get billets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBillets()
    {
        return $this->billets;
    }

    /**
     * Set prixTotal
     *
     * @param integer $prixTotal
     *
     * @return Commande
     */
    public function setPrixTotal($prixTotal)
    {
        $this->prixTotal = $prixTotal;

        return $this;
    }

    /**
     * Get prixTotal
     *
     * @return integer
     */
    public function getPrixTotal()
    {
        return $this->prixTotal;
    }
}
