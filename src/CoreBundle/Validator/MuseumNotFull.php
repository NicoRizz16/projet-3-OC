<?php
/**
 * Created by PhpStorm.
 * User: Nicolas
 * Date: 26/01/2017
 * Time: 07:12
 */

namespace CoreBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class MuseumNotFull extends Constraint
{
    public $message = "Vous ne pouvez plus réserver de billets en ligne pour ce jour, trop de billets ont déjà été vendus.";
}