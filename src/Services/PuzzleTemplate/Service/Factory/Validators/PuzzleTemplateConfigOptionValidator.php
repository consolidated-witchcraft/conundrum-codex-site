<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory\Validators;

use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigOptions\ConfigOptionTypeInvalidPatternException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigOptions\ConfigOptionTypeScalarMismatchException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigOptions\MissingConfigOptionSubfieldException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigOptions\UnknownConfigOptionTypeSpecifiedException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\UnknownFieldTypeSpecifiedException;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;

class PuzzleTemplateConfigOptionValidator
{
    /**
     * These fields are required in every config option
     */
    private const array REQUIRED_CONFIG_OPTION_FIELDS = [
        'configName' => 'configName',
        'label' => 'string',
        'type' => 'type',
    ];


    private const string CONFIG_NAME_REGEX = '/^[a-z0-9_]+$/';

    /**
     * @throws ConfigOptionTypeScalarMismatchException
     * @throws MissingConfigOptionSubfieldException
     * @throws ConfigOptionTypeInvalidPatternException
     * @throws UnknownFieldTypeSpecifiedException
     * @throws UnknownConfigOptionTypeSpecifiedException
     */
    public static function validateConfigOption(array $configOptionDefinition, string $moduleName): void {

        self::validateRequiredFieldKeys($configOptionDefinition, $moduleName);

        foreach (self::REQUIRED_CONFIG_OPTION_FIELDS as $fieldName => $type) {
            switch ($type) {
                case 'configName':
                    self::validateConfigNameField(
                        fieldName: $fieldName,
                        configName: $configOptionDefinition['configName'],
                        moduleName: $moduleName
                    );
                    break;

                case 'string':
                    self::validateStringField(
                        fieldName: $fieldName,
                        fieldValue: $configOptionDefinition[$fieldName],
                        moduleName: $moduleName,
                    );

                    break;

                case 'type':
                    self::validateConfigFieldTypeKnown($configOptionDefinition, $moduleName);
                    break;

                default:
                    throw new UnknownFieldTypeSpecifiedException(
                        unknownFieldType: $type,
                        moduleName: $moduleName,
                    );
            }
        }
    }


    /**
     * @throws ConfigOptionTypeInvalidPatternException
     */
    public static function validateConfigNameField(string $fieldName, string $configName, string $moduleName): void {
        if (!preg_match(self::CONFIG_NAME_REGEX, $configName)) {
            throw new ConfigOptionTypeInvalidPatternException(
                fieldName: $fieldName,
                moduleName: $moduleName,
                infoMessage: "Config name keys may only contain lowercase letters, numbers, and underscores."
            );
        }
    }

    /**
     * @throws MissingConfigOptionSubfieldException
     */
    private static function validateRequiredFieldKeys(array $configOptionDefinition, string $moduleName): void {
        foreach (self::REQUIRED_CONFIG_OPTION_FIELDS as $fieldName => $type) {
            if (!isset($configOptionDefinition[$fieldName])) {
                throw new MissingConfigOptionSubfieldException(
                    subfieldName: $fieldName,
                    moduleName: $moduleName
                );
            }
        }
    }

    /**
     * @throws UnknownConfigOptionTypeSpecifiedException
     */
    private static function validateConfigFieldTypeKnown(array $configOptionDefinition, string $moduleName): void {
        if (!in_array($configOptionDefinition['type'], ValidPuzzleConfigOptionTypes::KNOWN_CONFIG_OPTION_TYPES)) {
            throw new UnknownConfigOptionTypeSpecifiedException(
                unknownFieldType: (string) $configOptionDefinition['type'],
                configOptionName: $configOptionDefinition['configName'],
                moduleName: $moduleName,
            );
        }
    }

    /**
     * @throws ConfigOptionTypeScalarMismatchException
     */
    private static function validateStringField(string $fieldName, mixed $fieldValue, string $moduleName): void {
        if (!is_string($fieldValue)) {
            throw new ConfigOptionTypeScalarMismatchException(
                fieldName: $fieldName,
                expectedType: 'string',
                actualType: gettype($fieldValue),
                moduleName: $moduleName
            );
        }
    }
}
