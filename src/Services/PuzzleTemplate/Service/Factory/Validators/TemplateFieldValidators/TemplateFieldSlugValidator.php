<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\TemplateFieldValidators;

use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\TemplateFieldInvalidPatternException;

class TemplateFieldSlugValidator implements TemplateFieldValidatorInterface
{
    private const string SLUG_REGEX = '/^[a-z0-9_]+$/';

    /**
     * @throws TemplateFieldInvalidPatternException
     */
    public static function validate(mixed $value, string $fieldName, string $moduleName): void
    {
        if (!preg_match(self::SLUG_REGEX, $value)) {
            throw new TemplateFieldInvalidPatternException(
                fieldName: $fieldName,
                moduleName: $moduleName,
                infoMessage: "Slugs may only contain lowercase letters, numbers and underscores."
            );
        }
    }
}
