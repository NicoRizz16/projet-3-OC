<?php

namespace CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use CoreBundle\Validator\ReservationOpen;
use CoreBundle\Validator\MuseumNotFull;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Order
 *
 * @ORM\Table(name="order_table")
 * @ORM\Entity(repositoryClass="CoreBundle\Repository\OrderRepository")
 */
class Order
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
     * @ORM\Column(name="orderDate", type="datetime")
     */
    private $orderDate;

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
     * @ORM\OneToMany(targetEntity="CoreBundle\Entity\Ticket", mappedBy="order", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid
     */
    private $tickets;

    /**
     * @var int
     *
     * @ORM\Column(name="ticketsNb", type="smallint")
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
    private $ticketsNb;

    /**
     * @var string
     *
     * @ORM\Column(name="ticketType", type="string", length=255)
     * @Assert\Choice({"Journée", "Demi-journée"}, strict = true)
     */
    private $ticketType;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateVisit", type="datetime")
     * @Assert\Date()
     * @Assert\GreaterThanOrEqual("today")
     * @ReservationOpen()
     * @MuseumNotFull()
     */
    private $dateVisit;

    /**
     * @var int
     * @ORM\Column(name="totalPrice", type="integer")
     */
    private $totalPrice;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orderDate = new \Datetime();
        $this->tickets = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set orderDate
     *
     * @param \DateTime $orderDate
     *
     * @return Order
     */
    public function setOrderDate($orderDate)
    {
        $this->orderDate = $orderDate;

        return $this;
    }

    /**
     * Get orderDate
     *
     * @return \DateTime
     */
    public function getOrderDate()
    {
        return $this->orderDate;
    }

    /**
     * Set mail
     *
     * @param string $mail
     *
     * @return Order
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
     * Set ticketsNb
     *
     * @param integer $ticketsNb
     *
     * @return Order
     */
    public function setTicketsNb($ticketsNb)
    {
        $this->ticketsNb = $ticketsNb;

        return $this;
    }

    /**
     * Get ticketsNb
     *
     * @return int
     */
    public function getTicketsNb()
    {
        return $this->ticketsNb;
    }

    /**
     * Set ticketType
     *
     * @param string $ticketType
     *
     * @return Order
     */
    public function setTicketType($ticketType)
    {
        $this->ticketType = $ticketType;

        return $this;
    }

    /**
     * Get ticketType
     *
     * @return string
     */
    public function getTicketType()
    {
        return $this->ticketType;
    }

    /**
     * Set dateVisit
     *
     * @param \DateTime $dateVisit
     *
     * @return Order
     */
    public function setDateVisit($dateVisit)
    {
        $this->dateVisit = $dateVisit;

        return $this;
    }

    /**
     * Get dateVisit
     *
     * @return \DateTime
     */
    public function getDateVisit()
    {
        return $this->dateVisit;
    }

    /**
     * Add ticket
     *
     * @param \CoreBundle\Entity\Ticket $ticket
     *
     * @return Order
     */
    public function addTicket(\CoreBundle\Entity\Ticket $ticket)
    {
        $this->tickets[] = $ticket;
        $ticket->setOrder($this);
        return $this;
    }

    /**
     * Remove ticket
     *
     * @param \CoreBundle\Entity\Ticket $ticket
     */
    public function removeTicket(\CoreBundle\Entity\Ticket $ticket)
    {
        $this->tickets->removeElement($ticket);
    }

    /**
     * Get tickets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTickets()
    {
        return $this->tickets;
    }

    /**
     * Set totalPrice
     *
     * @param integer $totalPrice
     *
     * @return Order
     */
    public function setTotalPrice($totalPrice)
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    /**
     * Get totalPrice
     *
     * @return integer
     */
    public function getTotalPrice()
    {
        return $this->totalPrice;
    }

    /**
     * @Assert\Callback
     */
    public function isTicketTypeValid(ExecutionContextInterface $context)
    {
        $now = new \DateTime();
        $dateVisit = $this->getDateVisit();
        // Si la date entrée est différente de la date du jour, les 2 types de billets sont possible
        if($now->format('Y-m-d') != $dateVisit->format('Y-m-d')) {
            return;
        }

        // On vérifie qu'un billet de type journée n'est pas commandé le jour même après 14h.
        if ($this->getTicketType() == "Journée" && $now->format('G') >= 14){
            // La règle est violée, on définit l'erreur
            $context
                ->buildViolation('Vous ne pouvez pas réserver un billet de type "journée" le jour même après 14h.')
                ->atPath('ticketType')
                ->addViolation();
        }
    }
}
