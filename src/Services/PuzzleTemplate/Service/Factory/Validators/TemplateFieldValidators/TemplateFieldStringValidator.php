<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\TemplateFieldValidators;

use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\TemplateFieldScalarMismatchException;

class TemplateFieldStringValidator implements TemplateFieldValidatorInterface
{

    public static function validate(mixed $value, string $fieldName, string $moduleName): void
    {
        if (!is_string($value)) {
            throw new TemplateFieldScalarMismatchException(
                fieldName: $fieldName,
                expectedType: 'string',
                actualType: gettype($value),
                moduleName: $moduleName
            );
        }
    }
}
