<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\TemplateFieldValidators;

use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\TemplateFieldInvalidPatternException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\TemplateFieldScalarMismatchException;

class TemplateFieldElementNameValidator implements TemplateFieldValidatorInterface
{

    private const string ELEMENT_NAME_REGEX = '/^[a-z0-9-]+$/';

    /**
     * @throws TemplateFieldInvalidPatternException
     */
    public static function validate(mixed $value, string $fieldName, string $moduleName): void
    {
        if (!is_string($value)) {
            throw new TemplateFieldScalarMismatchException($fieldName, 'string', gettype($value), $moduleName);
        }

        if (preg_match(self::ELEMENT_NAME_REGEX, $value) !== 1) {
            throw new TemplateFieldInvalidPatternException(
                fieldName: $fieldName,
                moduleName: $moduleName,
                infoMessage: "Element names may only contain lowercase letters, numbers and hyphens."
            );
        }
    }
}
