<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 25/01/2017
 * Time: 15:33
 */

namespace CoreBundle\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\ConstraintValidator;

class ReservationOpenValidator extends ConstraintValidator
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

        // Si le jour est un mardi, un 1er mai, un 1er novembre ou un 25 décembre => le musée est fermé
        if ($jourSemaine == 2 || ($jour == 1 && ($mois == 5 || $mois == 11)) || ($jour == 25 && $mois == 12)){
            $this->context->buildViolation($constraint->museumClose)
                ->setParameter('%date%', $value->format('d-m-Y'))
                ->addViolation();
        }

        // Si le jour est un dimanche ou un jour férié où le musée est ouvert => La réservation en ligne est impossible
        if($jourSemaine == 0 || $this->joursFeriesOuvert($value)){
            $this->context->buildViolation($constraint->reservationClose)
                ->addViolation();
        }
    }

    // Méthode récupérant les jours fériés en France où le musée est ouvert mais pas le service de réservation en ligne
    public function joursFeriesOuvert(\DateTime $date){
        $EstUnJourFerieOuvert = false;
        $year = $date->format('Y');
        // On récupère le jour et le mois de Pâques pour l'année en cours
        $easterDate  = easter_date($year);

        $joursFeriesOuverts = array(
            // Dates fixes (la toussaint, la fête du travail et le jour de noël sont des jours fériés où le musée est fermé)
            new \DateTime($year.'-01-01'), // 1er janvier
            new \DateTime($year.'-05-08'), // Victoire des alliés
            new \DateTime($year.'-07-14'), // Fête nationale
            new \DateTime($year.'-08-15'), // Assomption
            new \DateTime($year.'-11-11'), // Armistice
            // 3 jours fériés variables en fonction du jour de Pâques de l'année en cours
            new \DateTime($year.'-'.date('n', $easterDate+(24*3600)).'-'.date('j', $easterDate+(24*3600))), // Lendemain du jour de Pâques
            new \DateTime($year.'-'.date('n', $easterDate+(39*24*3600)).'-'.date('j', $easterDate+(39*24*3600))), // 39 jours après Pâques
            new \DateTime($year.'-'.date('n', $easterDate+(50*24*3600)).'-'.date('j', $easterDate+(50*24*3600))) // 50 jours après Pâques
        );

        // Verification si la date correspond à un des jours fériés de la liste
        foreach ($joursFeriesOuverts as $jour){
            if($jour == $date){
                $EstUnJourFerieOuvert = true;
            }
        }

        return $EstUnJourFerieOuvert;
    }
}