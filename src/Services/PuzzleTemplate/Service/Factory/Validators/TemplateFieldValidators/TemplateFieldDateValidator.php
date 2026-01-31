<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\TemplateFieldValidators;

use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\InvalidTemplateFieldDateTimeException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\TemplateFieldValidators\TemplateFieldValidatorInterface;

class TemplateFieldDateValidator implements TemplateFieldValidatorInterface
{

    /**
     * @throws InvalidTemplateFieldDateTimeException
     */
    public static function validate(mixed $value, string $fieldName, string $moduleName): void
    {
        try {
            new \DateTime($value);
        } catch (\DateMalformedStringException $e) {
            throw new InvalidTemplateFieldDateTimeException($fieldName, $moduleName, $e->getCode(), $e);
        }
    }
}
