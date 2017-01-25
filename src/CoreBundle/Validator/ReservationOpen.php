<?php

namespace CoreBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ReservationOpen extends Constraint
{
    public $museumClose = 'Le musée n\'est pas ouvert le "%date%"';

    public $reservationClose = 'Il n\'est pas possible de réserver vos billets en ligne pour les dimanches et les jours fériés.';
}