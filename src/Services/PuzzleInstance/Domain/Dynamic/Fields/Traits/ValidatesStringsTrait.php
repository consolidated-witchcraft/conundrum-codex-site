<?php

namespace App\Services\PuzzleInstance\Domain\Dynamic\Fields\Traits;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldUnsafeValueException;

trait ValidatesStringsTrait
{
    public const string ALLOWED_CHARACTERS_PATTERN = '/^[A-Za-z0-9\- ,.!:;&]+$/';

    public const string URL_DETECTION_PATTERN  = '~\b(?:https?://)?(?:www\.)?[A-Za-z0-9.-]+\.[A-Za-z]{2,}(?:/[^\s]*)?~i';
    public const string IPV4_DETECTION_PATTERN = '~\b(?:\d{1,3}\.){3}\d{1,3}\b~';

    public const string IPV6_DETECTION_PATTERN = '~\b(?:\[[0-9A-Fa-f:]+\]|[0-9A-Fa-f:]{2,})\b~';

    protected function guardString(string $fieldName, string $value): void {
        if (preg_match(self::ALLOWED_CHARACTERS_PATTERN, $value) !== 1) {
            throw new DynamicFieldUnsafeValueException(
                fieldName:$fieldName,
                acceptedPattern: self::ALLOWED_CHARACTERS_PATTERN
            );
        }
        if (
            preg_match(self::URL_DETECTION_PATTERN, $value)
        ) {
            throw new DynamicFieldUnsafeValueException(
                fieldName: $fieldName,
                acceptedPattern: self::ALLOWED_CHARACTERS_PATTERN
            );
        }
        if (
            preg_match(self::IPV4_DETECTION_PATTERN, $value)
        ) {
            throw new DynamicFieldUnsafeValueException(
                fieldName: $fieldName,
                acceptedPattern: self::ALLOWED_CHARACTERS_PATTERN
            );
        }

        if (
            preg_match(self::IPV6_DETECTION_PATTERN, $value)
        ) {
            throw new DynamicFieldUnsafeValueException(
                fieldName: $fieldName,
                acceptedPattern: self::ALLOWED_CHARACTERS_PATTERN
            );
        }
    }
}
