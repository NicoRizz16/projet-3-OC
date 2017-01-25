<?php

namespace CoreBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class MuseumOpen extends Constraint
{
    public $message = 'Le musée n\'est pas ouvert le "%date%"';
}