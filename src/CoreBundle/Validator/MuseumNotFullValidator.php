<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 26/01/2017
 * Time: 07:13
 */

namespace CoreBundle\Validator;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MuseumNotFullValidator extends ConstraintValidator
{
    private $em;
    private $max_tickets_per_day;

    public function __construct(EntityManager $em, $max_tickets_per_day)
    {
        $this->em = $em;
        $this->max_tickets_per_day = $max_tickets_per_day;
    }

    public function Validate($value, Constraint $constraint)
    {
        if (!$value instanceof \DateTime) {
            return;
        }
        // Récupération du nombre de tickets vendus pour la date demandée.
        $repository = $this->em->getRepository('CoreBundle:Order');
        $sumTicketsSold = $repository->sumTicketsSold($value);

        // Si plus de tickets que le nombre maximum définit ont déjà été vendus => le service de réservation en ligne est fermé pour ce jour.
        if($sumTicketsSold>$this->max_tickets_per_day){
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }

    }
}