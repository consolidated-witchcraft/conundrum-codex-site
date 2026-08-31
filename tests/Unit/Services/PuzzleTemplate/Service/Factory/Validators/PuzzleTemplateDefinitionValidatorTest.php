<?php

use App\Services\PuzzleTemplate\Service\Factory\Exceptions\PuzzleTemplateRegistryBuildException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigOptions\ConfigOptionTypeInvalidPatternException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigOptions\ConfigOptionTypeScalarMismatchException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\InvalidTemplateFieldDateTimeException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\InvalidTemplateFieldEmailAddressException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\MissingRequiredTemplateFieldException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\TemplateFieldInvalidPatternException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\TemplateFieldScalarMismatchException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\UnknownFieldSpecifiedException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\PuzzleTemplateDefinitionValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;


beforeEach(function () {
    $router = $this->createStub(RouterInterface::class);
    $router->method('generate')->willReturnCallback(
        function ($value) {
            if ($value === 'nosuchRoute') {
                throw new RouteNotFoundException;
            }
            return $value;
        }
    );
    $this->puzzleTemplateDefinitionValidator = new PuzzleTemplateDefinitionValidator(
        $router
    );
});
function generateTestData(array $overrides = [], array $fieldKeysToUnset = []): array
{
    $templateData = [
        'title' => $overrides['title'] ?? 'Test Title',
        'created' => $overrides['created'] ?? '2026-01-01 00:00:00',
        'slug' => $overrides['slug'] ?? 'test_slug',
        'description' => $overrides['description'] ?? 'Test Description',
        'categories' => $overrides['categories'] ?? [
            'category_1',
            'category_2'
        ],
        'type' => $overrides['type'] ?? 'misc',
        'hidden' => $overrides['hidden'] ?? false,
        'static' => $overrides['static'] ?? false,
        'staticCreateRoute' => $overrides['staticCreateRoute'] ?? 'some.route',
        'staticEditRoute' => $overrides['staticEditRoute'] ?? 'some.route',
        'staticPlayRoute' => $overrides['staticPlayRoute'] ?? 'some.route',
        'creator' => $overrides['creator'] ?? "test@test.dev",
        'configOptions' => $overrides['configOptions'] ?? [
        ],
        'credits' => $overrides['credits'] ?? [],
        'elementName' => $overrides['elementName'] ?? 'test-element',
    ];
    foreach ($fieldKeysToUnset as $keyToUnset) {
        if (isset($templateData[$keyToUnset])) {
            unset($templateData[$keyToUnset]);
        }
    }
    return $templateData;
}

dataset('provideValidateRequiredFieldsPresentTestData', function () {
    yield 'no_title' => [
        'missingFieldName' => 'title'
    ];

    yield 'no_created_date' => [
        'missingFieldName' => 'created'
    ];

    yield 'no_slug' => [
        'missingFieldName' => 'slug'
    ];

    yield 'no_description' => [
        'missingFieldName' => 'description'
    ];

    yield 'no_categories' => [
        'missingFieldName' => 'categories'
    ];

    yield 'no_type' => [
        'missingFieldName' => 'type'
    ];

    yield 'no_static' => [
        'missingFieldName' => 'static'
    ];

    yield 'no_hidden' => [
        'missingFieldName' => 'hidden'
    ];

    yield 'no_element_name' => [
        'missingFieldName' => 'elementName'
    ];
});

test('validate required fields present', function (string $missingFieldName) {
    $templateData = generateTestData(fieldKeysToUnset: [$missingFieldName]);
    $moduleName = 'testmodule';
    $this->expectException(MissingRequiredTemplateFieldException::class,);
    $this->expectExceptionMessage(sprintf(MissingRequiredTemplateFieldException::ERROR_MESSAGE_PATTERN, $missingFieldName, $moduleName));
    $this->puzzleTemplateDefinitionValidator->validateJson($templateData, $moduleName);
})->with('provideValidateRequiredFieldsPresentTestData');

test('validate all fields known', function () {
    $templateData = generateTestData();
    $moduleName = 'testmodule';
    $templateData['unknownFieldName'] = 'unknownFieldName';
    $this->expectException(UnknownFieldSpecifiedException::class);
    $this->expectExceptionMessage("An unknown field named 'unknownFieldName' was discovered in the template.json for module 'testmodule'");
    $this->puzzleTemplateDefinitionValidator->validateJson($templateData, $moduleName);
});

dataset('provideValidateFieldsData', function () {
    yield 'invalid_string' => [
        'overrides' => [
            'title' => false
        ],
        'expectedExceptionClass' => TemplateFieldScalarMismatchException::class,
        'expectedExceptionMessage' => "The field 'title' defined in module 'testmodule' is invalid. Its expected type is string. Its actual type is boolean."
    ];

    yield 'invalid_slug' => [
        'overrides' => [
            'slug' => "WHAat m@dn355 i5 th!s?"
        ],
        'expectedExceptionClass' => TemplateFieldInvalidPatternException::class,
        'expectedExceptionMessage' => "The field definition 'slug' in template 'testmodule' is invalid: Slugs may only contain lowercase letters, numbers and underscores."
    ];

    yield 'invalid_array' => [
        'overrides' => [
            'categories' => 'LOL'
        ],
        'expectedExceptionClass' => TemplateFieldScalarMismatchException::class,
        'expectedExceptionMessage' => "The field 'categories' defined in module 'testmodule' is invalid. Its expected type is array. Its actual type is string."
    ];

    yield 'invalid_email' => [
        'overrides' => [
            'creator' => 'thisisnotanemailaddress'
        ],
        'expectedExceptionClass' => InvalidTemplateFieldEmailAddressException::class,
        'expectedExceptionMessage' => "The field 'creator' defined in template 'testmodule' is not a valid email address."
    ];

    yield 'invalid_date' => [
        'overrides' => [
            'created' => 'thisisnotadatestring'
        ],
        'expectedExceptionClass' => InvalidTemplateFieldDateTimeException::class,
        'expectedExceptionMessage' => "The field 'created' defined in template 'testmodule' is not a valid date time string."
    ];

    yield 'invalid_boolean' => [
        'overrides' => [
            'static' => 'BLAH BLAH BLAH'
        ],
        'expectedExceptionClass' => TemplateFieldScalarMismatchException::class,
        'expectedExceptionMessage' => "The field 'static' defined in module 'testmodule' is invalid. Its expected type is boolean. Its actual type is string."
    ];

    yield 'invalid_element_name' => [
        'overrides' => [
            'elementName' => 'This is not a valid Element name',
        ],
        'expectedExceptionClass' => TemplateFieldInvalidPatternException::class,
        'expectedExceptionMessage' => 'Element names may only contain lowercase letters, numbers and hyphens'
    ];
});

test('validate fields', function (array $overrides, string $expectedExceptionClass, string $expectedExceptionMessage) {
    $templateData = generateTestData($overrides);
    $this->expectException($expectedExceptionClass);
    $this->expectExceptionMessage($expectedExceptionMessage);
    $this->puzzleTemplateDefinitionValidator->validateJson($templateData, 'testmodule');
})->with('provideValidateFieldsData');
