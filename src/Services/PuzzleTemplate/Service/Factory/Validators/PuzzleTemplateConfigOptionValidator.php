<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory\Validators;

use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigFieldTypeInvalidPatternException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigFieldTypeScalarMismatchException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\InvalidConfigOptionDefinitionException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\MissingConfigOptionSubfieldException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\UnknownConfigFieldTypeSpecifiedException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\UnknownFieldSpecifiedException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\UnknownFieldTypeSpecifiedException;

class PuzzleTemplateConfigOptionValidator
{
    private const array REQUIRED_CONFIG_OPTION_FIELDS = [
        'configName' => 'configName',
        'label' => 'string',
        'type' => 'type'
    ];

    private const array KNOWN_CONFIG_OPTION_TYPES = [
        'text',
        'stringArray',
        'dieRoll',
        'route'
    ];

    private const string CONFIG_NAME_REGEX = '/^[a-z0-9_]+$/';

    /**
     * @throws UnknownFieldSpecifiedException
     * @throws ConfigFieldTypeScalarMismatchException
     * @throws MissingConfigOptionSubfieldException
     * @throws ConfigFieldTypeInvalidPatternException
     * @throws UnknownFieldTypeSpecifiedException
     * @throws UnknownConfigFieldTypeSpecifiedException
     */
    public function validateConfigOption(array $configOptionDefinition, string $moduleName): void {

        $this->validateRequiredFieldKeys($configOptionDefinition, $moduleName);
        $this->validateConfigFieldTypesKnown($configOptionDefinition, $moduleName);
        foreach (self::REQUIRED_CONFIG_OPTION_FIELDS as $fieldName => $type) {
            switch ($type) {
                case 'configName':
                    if (!preg_match(self::CONFIG_NAME_REGEX, $configOptionDefinition['configName'])) {
                        throw new ConfigFieldTypeInvalidPatternException(
                            fieldName: $fieldName,
                            moduleName: $moduleName,
                            infoMessage: "Config name keys may only contain lowercase letters, numbers, and underscores."
                        );
                    }
                    break;

                case 'string':
                    if (!is_string($configOptionDefinition[$fieldName])) {
                        throw new ConfigFieldTypeScalarMismatchException(
                            fieldName: $fieldName,
                            expectedType: 'string',
                            actualType: gettype($configOptionDefinition[$fieldName]),
                            moduleName: $moduleName
                        );
                    }
                    break;

                case 'type':
                    if (!in_array($configOptionDefinition[$fieldName], self::KNOWN_CONFIG_OPTION_TYPES)) {
                        throw new UnknownFieldSpecifiedException(
                            unknownFieldName: $fieldName,
                            moduleName: $moduleName,
                        );
                    }
                    break;

                case 'boolean':
                    if (!is_bool($configOptionDefinition[$fieldName])) {
                        throw new ConfigFieldTypeScalarMismatchException(
                            fieldName: $fieldName,
                            expectedType: 'boolean',
                            actualType: gettype($configOptionDefinition[$fieldName]),
                            moduleName: $moduleName,
                        );
                    }
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
     * @throws MissingConfigOptionSubfieldException
     */
    private function validateRequiredFieldKeys(array $configOptionDefinition, string $moduleName): void {
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
     * @throws UnknownConfigFieldTypeSpecifiedException
     */
    private function validateConfigFieldTypesKnown(array $configOptionDefinition, string $moduleName): void {
        foreach (self::REQUIRED_CONFIG_OPTION_FIELDS as $fieldName => $type) {
            if (!in_array($configOptionDefinition['type'], self::KNOWN_CONFIG_OPTION_TYPES)) {
                throw new UnknownConfigFieldTypeSpecifiedException(
                    unknownFieldType: (string) $configOptionDefinition['type'],
                    configOptionName: $configOptionDefinition['configName'],
                    moduleName: $moduleName,
                );
            }
        }
    }
}
