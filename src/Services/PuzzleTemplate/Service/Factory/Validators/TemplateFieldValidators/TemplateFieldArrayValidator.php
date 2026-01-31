<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\TemplateFieldValidators;

use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\TemplateFieldScalarMismatchException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\TemplateFieldValidators\TemplateFieldValidatorInterface;

class TemplateFieldArrayValidator implements TemplateFieldValidatorInterface
{

    /**
     * @throws TemplateFieldScalarMismatchException
     */
    public static function validate(mixed $value, string $fieldName, string $moduleName): void
    {
        if (!is_array($value)) {
            throw new TemplateFieldScalarMismatchException(
                fieldName: $fieldName,
                expectedType: 'array',
                actualType: gettype($value),
                moduleName: $moduleName
            );
        }
    }
}
