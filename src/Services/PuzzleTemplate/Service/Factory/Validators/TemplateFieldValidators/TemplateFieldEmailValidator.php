<?php

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\TemplateFieldValidators;

use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\InvalidTemplateFieldEmailAddressException;

class TemplateFieldEmailValidator implements TemplateFieldValidatorInterface
{

    public static function validate(mixed $value, string $fieldName, string $moduleName): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidTemplateFieldEmailAddressException($fieldName, $moduleName);
        }
    }
}
