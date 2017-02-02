<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Billet
 *
 * @ORM\Table(name="billet")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\BilletRepository")
 */
class Billet
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
     * @ORM\ManyToOne(targetEntity="CoreBundle\Entity\Commande", inversedBy="billets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $commande;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     * @Assert\Length(
     *     min = 2,
     *     max = 50,
     *     minMessage = "Votre nom doit faire au minimum 2 caractères.",
     *     maxMessage = "Votre nom doit faire au maximum 50 caractères."
     * )
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     * @Assert\Length(
     *     min = 2,
     *     max = 50,
     *     minMessage = "Votre prenom doit faire au minimum 2 caractères.",
     *     maxMessage = "Votre prenom doit faire au maximum 50 caractères."
     * )
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=255)
     * @Assert\Country()
     */
    private $pays;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateNaissance", type="date")
     * @Assert\Date()
     * @Assert\LessThanOrEqual("today")
     *
     */
    private $dateNaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="tarif", type="string", length=255)
     */
    private $tarif;

    /**
     * @var boolean
     *
     * @ORM\Column(name="tarif_reduit", type="boolean")
     * @Assert\Type("bool")
     */
    private $tarifReduit;

    /**
     * @var integer
     *
     * @ORM\Column(name="prix", type="smallint")
     */
    private $prix;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, unique=true)
     */
    private $code;


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
     * Set nom
     *
     * @param string $nom
     *
     * @return Billet
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Billet
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return Billet
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set dateNaissance
     *
     * @param \DateTime $dateNaissance
     *
     * @return Billet
     */
    public function setDateNaissance($dateNaissance)
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    /**
     * Get dateNaissance
     *
     * @return \DateTime
     */
    public function getDateNaissance()
    {
        return $this->dateNaissance;
    }

    /**
     * Set tarif
     *
     * @param string $tarif
     *
     * @return Billet
     */
    public function setTarif($tarif)
    {
        $this->tarif = $tarif;

        return $this;
    }

    /**
     * Get tarif
     *
     * @return string
     */
    public function getTarif()
    {
        return $this->tarif;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Billet
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set tarifReduit
     *
     * @param boolean $tarifReduit
     *
     * @return Billet
     */
    public function setTarifReduit($tarifReduit)
    {
        $this->tarifReduit = $tarifReduit;

        return $this;
    }

    /**
     * Get tarifReduit
     *
     * @return boolean
     */
    public function getTarifReduit()
    {
        return $this->tarifReduit;
    }

    /**
     * Set prix
     *
     * @param integer $prix
     *
     * @return Billet
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;

        return $this;
    }

    /**
     * Get prix
     *
     * @return integer
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * Set commande
     *
     * @param \CoreBundle\Entity\Commande $commande
     *
     * @return Billet
     */
    public function setCommande(\CoreBundle\Entity\Commande $commande)
    {
        $this->commande = $commande;

        return $this;
    }

    /**
     * Get commande
     *
     * @return \CoreBundle\Entity\Commande
     */
    public function getCommande()
    {
        return $this->commande;
    }
}
