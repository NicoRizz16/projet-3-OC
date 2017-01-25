<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 25/01/2017
 * Time: 15:33
 */

namespace CoreBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class MuseumOpenValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$value instanceof \DateTime) {
            return;
        }

        // Récupération du jour de la semaine (0 = dimanche et 6 = samedi)
        $jourSemaine = $value->format('w');
        // Récupération du jour du mois
        $jour = $value->format('j');
        // Récupération du mois
        $mois = $value->format('n');

        // Si le jour est un mardi, un 1er mai, un 1er novembre ou un 25 décembre => jours de fermeture du musée
        if ($jourSemaine == 2 || ($jour == 1 && ($mois == 5 || $mois == 11)) || ($jour == 25 && $mois == 12)){
            $this->context->buildViolation($constraint->message)
                ->setParameter('%date%', $value->format('d-m-Y'))
                ->addViolation();
        }
    }
}