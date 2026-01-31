<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\TemplateFieldValidators;

interface TemplateFieldValidatorInterface
{
    public static function validate(mixed $value, string $fieldName, string $moduleName): void;
}
