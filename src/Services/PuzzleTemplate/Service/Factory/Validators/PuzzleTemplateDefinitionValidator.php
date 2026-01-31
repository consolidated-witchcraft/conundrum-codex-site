<?php

declare(strict_types=1);

namespace App\Services\PuzzleTemplate\Service\Factory\Validators;

use App\Services\PuzzleTemplate\Service\Factory\Exceptions\PuzzleTemplateRegistryBuildException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigOptions\ConfigOptionTypeInvalidPatternException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigOptions\ConfigOptionTypeScalarMismatchException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\InvalidTemplateFieldRouteException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\MissingRequiredTemplateFieldException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\MissingTemplateFieldStaticRouteException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\TemplateFieldScalarMismatchException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\UnknownFieldSpecifiedException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\TemplateFieldValidators\TemplateFieldArrayValidator;
use App\Services\PuzzleTemplate\Service\Factory\Validators\TemplateFieldValidators\TemplateFieldBooleanValidator;
use App\Services\PuzzleTemplate\Service\Factory\Validators\TemplateFieldValidators\TemplateFieldDateValidator;
use App\Services\PuzzleTemplate\Service\Factory\Validators\TemplateFieldValidators\TemplateFieldElementNameValidator;
use App\Services\PuzzleTemplate\Service\Factory\Validators\TemplateFieldValidators\TemplateFieldEmailValidator;
use App\Services\PuzzleTemplate\Service\Factory\Validators\TemplateFieldValidators\TemplateFieldSlugValidator;
use App\Services\PuzzleTemplate\Service\Factory\Validators\TemplateFieldValidators\TemplateFieldStringValidator;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

class PuzzleTemplateDefinitionValidator
{
    public const array REQUIRED_TEMPLATE_FIELDS = [
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

    private const array REQUIRED_STATIC_ROUTE_KEYS = [
        'staticCreateRoute',
        'staticEditRoute',
        'staticPlayRoute'
    ];

    public function __construct(
        private readonly RouterInterface $router
    ) {}

    /**
     * @throws UnknownFieldSpecifiedException
     * @throws MissingRequiredTemplateFieldException
     * @throws ConfigOptionTypeScalarMismatchException
     * @throws ConfigOptionTypeInvalidPatternException
     * @throws PuzzleTemplateRegistryBuildException
     */
    public function validateJson(array $decodedJson, string $moduleName): void {
        self::validateRequiredFieldsPresent($decodedJson, $moduleName);
        self::validateAllFieldsKnown($decodedJson, $moduleName);
        self::validateStaticRoutes($decodedJson, $moduleName);
        $this->validateFields($decodedJson, $moduleName);
    }

    /**
     * @throws MissingRequiredTemplateFieldException
     */
    private static function validateRequiredFieldsPresent(array $decodedJson, string $moduleName): void {
        foreach (self::REQUIRED_TEMPLATE_FIELDS as $requiredField) {
            if (!isset($decodedJson[$requiredField])) {
                throw new MissingRequiredTemplateFieldException($requiredField, $moduleName);
            }
        }
    }

    /**
     * @throws UnknownFieldSpecifiedException
     */
    private static function validateAllFieldsKnown(array $decodedJson, string $moduleName): void {
        foreach ($decodedJson as $key => $definition) {
            if (!in_array($key, array_keys(self::KNOWN_TEMPLATE_FIELDS))) {
                throw new UnknownFieldSpecifiedException($key, $moduleName);
            }
        }
    }

    public static function validateStaticRoutes(array $decodedJson, string $moduleName): void {
        if ($decodedJson['static'] === true) {
            foreach (self::REQUIRED_STATIC_ROUTE_KEYS as $requireStaticRouteKey) {
                if (!isset($decodedJson[$requireStaticRouteKey]) || !is_string($decodedJson[$requireStaticRouteKey])) {
                    throw new MissingTemplateFieldStaticRouteException(
                        moduleName: $moduleName,
                        routeKey: $requireStaticRouteKey
                    );
                }
            }
        }
    }

    /**
     * @throws ConfigOptionTypeInvalidPatternException
     * @throws ConfigOptionTypeScalarMismatchException
     * @throws PuzzleTemplateRegistryBuildException
     */
    private function validateFields(array $decodedJson, string $moduleName): void {
        foreach (self::KNOWN_TEMPLATE_FIELDS as $fieldName => $fieldType) {
            $value = $decodedJson[$fieldName] ?? null;
            switch ($fieldType) {
                case 'string':
                    TemplateFieldStringValidator::validate(
                        value: $value,
                        fieldName: $fieldName,
                        moduleName: $moduleName
                    );
                    break;

                case 'slug':
                    TemplateFieldSlugValidator::validate(
                        value: $value,
                        fieldName: $fieldName,
                        moduleName: $moduleName
                    );
                    break;
                case 'array':
                    TemplateFieldArrayValidator::validate(
                        value: $value,
                        fieldName: $fieldName,
                        moduleName: $moduleName
                    );
                    break;
                case 'email':
                    TemplateFieldEmailValidator::validate(
                        value: $value,
                        fieldName: $fieldName,
                        moduleName: $moduleName
                    );
                    break;
                case 'date':
                    TemplateFieldDateValidator::validate(
                        value: $value,
                        fieldName: $fieldName,
                        moduleName: $moduleName
                    );
                    break;
                case 'boolean':
                    TemplateFieldBooleanValidator::validate(
                        value: $value,
                        fieldName: $fieldName,
                        moduleName: $moduleName
                    );

                    break;
                case 'configOptions':
                    if (!is_array($value)) {
                        throw new TemplateFieldScalarMismatchException(
                            fieldName: $fieldName,
                            expectedType: 'array',
                            actualType: gettype($value),
                            moduleName: $moduleName
                        );
                    } else {
                        foreach ($value as $configOption) {
                            PuzzleTemplateConfigOptionValidator::validateConfigOption($configOption, $moduleName);
                        }
                    }
                    break;

                case 'route':
                    if ($value === null) {
                        break;
                    }
                    try {
                        $this->router->generate($value, [
                            'gameSlug' => 'test',
                            'templateSlug' => 'test',
                            'instanceCode' => 'test'
                        ]);
                    } catch (RouteNotFoundException $e) {
                        throw new InvalidTemplateFieldRouteException($value, $fieldName, $moduleName, $e->getCode(), $e);
                    }

                    break;
                case 'elementName':
                    TemplateFieldElementNameValidator::validate(
                        value: $value,
                        fieldName: $fieldName,
                        moduleName: $moduleName
                    );
                    break;
            }
        }
    }
}
