<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;


#[\Attribute]
class EmailAddressIsNotBlockedConstraint extends Constraint
{
    public string $message = 'The e-mail address {{ emailAddress }} is blocked and cannot be used.';
}
