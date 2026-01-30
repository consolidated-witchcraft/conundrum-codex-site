<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory\Validators;

use App\Services\PuzzleTemplate\Service\Factory\Exceptions\PuzzleTemplateRegistryBuildException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigFieldTypeInvalidPatternException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigFieldTypeScalarMismatchException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\InvalidDateTimeException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\InvalidEmailAddressException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\InvalidRouteException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\MissingRequiredFieldException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\MissingStaticRouteException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\UnknownFieldSpecifiedException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

class PuzzleTemplateDefinitionValidator
{
    private const string SLUG_REGEX = '/^[a-z0-9_]+$/';

    private const string ELEMENT_NAME_REGEX = '/^[a-z0-9-]+$/';

    public const array REQUIRED_FIELDS = [
        'title',
        'created',
        'slug',
        'description',
        'categories',
        'type',
        'static',
        'creator',
        'configOptions',
        'elementName',
    ];

    private const array KNOWN_TEMPLATE_FIELDS = [
        'title' => 'string',
        'created' => 'date',
        'slug' => 'slug',
        'description' => 'string',
        'categories' => 'array',
        'type' => 'string',
        'static' => 'boolean',
        'staticCreateRoute' => 'route',
        'staticEditRoute' => 'route',
        'staticPlayRoute' => 'route',
        'creator' => 'email',
        'credits' => 'array',
        'configOptions' => 'configOptions',
        'elementName' => 'elementName'
    ];

    public function __construct(
        private readonly RouterInterface $router,
        private readonly PuzzleTemplateConfigOptionValidator $configOptionValidator
    ) {}

    /**
     * @throws UnknownFieldSpecifiedException
     * @throws MissingRequiredFieldException
     * @throws ConfigFieldTypeScalarMismatchException
     * @throws ConfigFieldTypeInvalidPatternException
     * @throws PuzzleTemplateRegistryBuildException
     */
    public function validateJson(array $decodedJson, string $moduleName): void {
        $this->validateRequiredFieldsPresent($decodedJson, $moduleName);
        $this->validateAllFieldsKnown($decodedJson, $moduleName);
        $this->validateFields($decodedJson, $moduleName);
    }

    /**
     * @throws MissingRequiredFieldException
     */
    private function validateRequiredFieldsPresent(array $decodedJson, string $moduleName): void {
        foreach (self::REQUIRED_FIELDS as $requiredField) {
            if (!isset($decodedJson[$requiredField])) {
                throw new MissingRequiredFieldException($requiredField, $moduleName);
            }
        }
    }

    /**
     * @throws UnknownFieldSpecifiedException
     */
    private function validateAllFieldsKnown(array $decodedJson, string $moduleName): void {
        foreach ($decodedJson as $key => $definition) {
            if (!in_array($key, array_keys(self::KNOWN_TEMPLATE_FIELDS))) {
                throw new UnknownFieldSpecifiedException($key, $moduleName);
            }
        }
    }

    /**
     * @throws ConfigFieldTypeInvalidPatternException
     * @throws ConfigFieldTypeScalarMismatchException
     * @throws PuzzleTemplateRegistryBuildException
     */
    private function validateFields(array $decodedJson, string $moduleName): void {
        foreach (self::KNOWN_TEMPLATE_FIELDS as $fieldName => $fieldType) {
            $value = $decodedJson[$fieldName] ?? null;
            switch ($fieldType) {
                case 'string':
                    if (!is_string($value)) {
                        throw new ConfigFieldTypeScalarMismatchException(
                            fieldname: $fieldName,
                            expectedType: 'string',
                            actualType: gettype($value),
                            moduleName: $moduleName
                        );
                    }
                    break;

                case 'slug':
                    if (!preg_match(self::SLUG_REGEX, $value)) {
                        throw new ConfigFieldTypeInvalidPatternException(
                            fieldName: $fieldName,
                            moduleName: $moduleName,
                            infoMessage: "Slugs may only contain lowercase letters, numbers and underscores."
                        );
                    }
                    break;
                case 'array':
                    if (!is_array($value)) {
                        throw new ConfigFieldTypeScalarMismatchException(
                            fieldName: $fieldName,
                            expectedType: 'array',
                            actualType: gettype($value),
                            moduleName: $moduleName
                        );
                    }
                    break;
                case 'email':
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        throw new InvalidEmailAddressException($fieldName, $moduleName);
                    }
                    break;
                case 'date':
                    try {
                        new \DateTime($value);
                    } catch (\DateMalformedStringException $e) {
                        throw new InvalidDateTimeException($fieldName, $moduleName);
                    }
                    break;
                case 'boolean':
                    if (!is_bool($value)) {
                        throw new ConfigFieldTypeScalarMismatchException(
                            fieldName: $fieldName,
                            expectedType: 'boolean',
                            actualType: gettype($value),
                            moduleName: $moduleName
                        );
                    }
                    break;
                case 'configOptions':
                    if (!is_array($value)) {
                        throw new ConfigFieldTypeScalarMismatchException(
                            fieldName: $fieldName,
                            expectedType: 'array',
                            actualType: gettype($value),
                            moduleName: $moduleName
                        );
                    } else {
                        foreach ($value as $configOption) {
                            $this->configOptionValidator->validateConfigOption($configOption, $moduleName);
                        }
                    }
                    break;

                case 'route':
                    if ($value === null) {
                        if ($decodedJson['static'] === true) {
                            throw new MissingStaticRouteException($moduleName);
                        } else {
                            break;
                        }
                    }
                    try {
                        $this->router->generate($value, [
                            'gameSlug' => 'test',
                            'templateSlug' => 'test',
                            'instanceCode' => 'test'
                        ]);
                    } catch (RouteNotFoundException $e) {
                        throw new InvalidRouteException($value, $moduleName);
                    }

                    break;
                case 'elementName':
                    if (preg_match(self::ELEMENT_NAME_REGEX, $value) !== 1) {
                        throw new ConfigFieldTypeInvalidPatternException(
                            fieldName: $fieldName,
                            moduleName: $moduleName,
                            infoMessage: "Element names may only contain lowercase letters, numbers and hyphens."
                        );
                    }
                    break;
                default:
                    throw new UnknownFieldSpecifiedException(
                        $fieldName,
                        $moduleName,
                    );
            }
        }
    }
}
