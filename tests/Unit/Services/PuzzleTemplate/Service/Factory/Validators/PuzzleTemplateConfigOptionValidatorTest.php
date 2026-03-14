<?php

declare(strict_types=1);
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigOptions\ConfigOptionTypeInvalidPatternException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigOptions\ConfigOptionTypeScalarMismatchException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigOptions\MissingConfigOptionSubfieldException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigOptions\UnknownConfigOptionTypeSpecifiedException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\UnknownFieldSpecifiedException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\TemplateFields\UnknownFieldTypeSpecifiedException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\PuzzleTemplateConfigOptionValidator;
use PHPUnit\Framework\Attributes\DataProvider;
beforeEach(function () {
    $this->validator = new PuzzleTemplateConfigOptionValidator();
});
dataset('provideFieldKeyTestData', function () {
    yield 'missing_config_name' => [
        'configData' => [
            'label' => 'testLabel',
            'type' => 'text'
        ],
        'moduleName' => 'testmodule',
        'expectedExceptionClass' => MissingConfigOptionSubfieldException::class,
        'expectedExceptionMessage' => "Template 'testmodule' is invalid. The required subfield 'configName' is missing"
    ];
    yield 'missing_label' => [
        'configData' => [
            'configName' => 'testname',
            'type' => 'text'
        ],
        'moduleName' => 'testmodule',
        'expectedExceptionClass' => MissingConfigOptionSubfieldException::class,
        'expectedExceptionMessage' => "Template 'testmodule' is invalid. The required subfield 'label' is missing"
    ];

    yield 'missing_type' => [
        'configData' => [
            'configName' => 'testname',
            'label' => 'testLabel',
        ],
        'moduleName' => 'testmodule',
        'expectedExceptionClass' => MissingConfigOptionSubfieldException::class,
        'expectedExceptionMessage' => "Template 'testmodule' is invalid. The required subfield 'type' is missing"
    ];
});
test('validate field keys', function (array $configData, string $moduleName, string $expectedExceptionClass, string $expectedExceptionMessage) {
    $this->expectException($expectedExceptionClass);
    $this->expectExceptionMessage($expectedExceptionMessage);
    $this->validator->validateConfigOption($configData, $moduleName);
})->with('provideFieldKeyTestData');
test('validate field unknown type', function () {
    $this->expectException(UnknownConfigOptionTypeSpecifiedException::class);
    $this->expectExceptionMessage("An unknown subfield type 'unknowntype' was discovered in the config option named 'testname' in module 'testmodule'");
    $this->validator->validateConfigOption(
        [
            'label' => 'testLabel',
            'type' => 'unknowntype',
            'configName' => 'testname'
        ],
        'testmodule'
    );
});
dataset('provideInvalidFieldTypeScalarTestData', function () {
    yield 'invalid_config_name' => [
        'configData' => [
            'label' => 'testLabel',
            'type' => 'text',
            'configName' => 'testOName'
        ],
        'moduleName' => 'testmodule',
        'expectedExceptionClass' => ConfigOptionTypeInvalidPatternException::class,
        'expectedExceptionMessage' => "The field definition 'configName' in template 'testmodule' is invalid: Config name keys may only contain lowercase letters, numbers, and underscores."
    ];

    yield 'string_type_mismatch' => [
        'configData' => [
            'label' => false,
            'type' => 'text',
            'configName' => 'testname'
        ],
        'moduleName' => 'testmodule',
        'expectedExceptionClass' => ConfigOptionTypeScalarMismatchException::class,
        'expectedExceptionMessage' => "The field 'label' defined in module 'testmodule' is invalid. Its expected type is string. Its actual type is boolean."
    ];
});
test('validate field type compliance', function (array $configData, string $moduleName, string $expectedExceptionClass, string $expectedExceptionMessage) {
    $this->expectException($expectedExceptionClass);
    $this->expectExceptionMessage($expectedExceptionMessage);
    $this->validator->validateConfigOption($configData, $moduleName);
})->with('provideInvalidFieldTypeScalarTestData');
