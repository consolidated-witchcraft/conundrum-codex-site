<?php

namespace App\Services\PuzzleTemplate\Service\Factory\Validators\TemplateFieldValidators;

interface TemplateFieldValidatorInterface
{
    public static function validate(mixed $value, string $fieldName, string $moduleName): void;
}
