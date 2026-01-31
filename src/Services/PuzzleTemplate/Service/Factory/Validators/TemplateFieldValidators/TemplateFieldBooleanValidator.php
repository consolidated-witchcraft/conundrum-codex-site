<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\TemplateFieldValidators;

use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\TemplateFieldScalarMismatchException;

class TemplateFieldBooleanValidator implements TemplateFieldValidatorInterface
{

    /**
     * @throws TemplateFieldScalarMismatchException
     */
    public static function validate(mixed $value, string $fieldName, string $moduleName): void
    {
        if (!is_bool($value)) {
            throw new TemplateFieldScalarMismatchException(
                fieldName: $fieldName,
                expectedType: 'boolean',
                actualType: gettype($value),
                moduleName: $moduleName
            );
        }
    }
}
