<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\PuzzleTemplate\Service\Factory\Validators;

use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigFieldTypeInvalidPatternException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\ConfigFieldTypeScalarMismatchException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\InvalidConfigOptionDefinitionException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\MissingConfigOptionSubfieldException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\UnknownConfigFieldTypeSpecifiedException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\UnknownFieldSpecifiedException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\Exceptions\UnknownFieldTypeSpecifiedException;
use App\Services\PuzzleTemplate\Service\Factory\Validators\PuzzleTemplateConfigOptionValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class PuzzleTemplateConfigOptionValidatorTest extends TestCase
{
    private PuzzleTemplateConfigOptionValidator $validator;
    public function setUp(): void {
        $this->validator = new PuzzleTemplateConfigOptionValidator();
    }

    public static function provideFieldKeyTestData(): iterable {
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
    }

    /**
     * @param array $configData
     * @param string $moduleName
     * @param string $expectedExceptionClass
     * @param string $expectedExceptionMessage
     * @return void
     * @throws MissingConfigOptionSubfieldException
     * @throws ConfigFieldTypeInvalidPatternException
     * @throws ConfigFieldTypeScalarMismatchException
     * @throws InvalidConfigOptionDefinitionException
     * @throws UnknownFieldSpecifiedException
     * @throws UnknownFieldTypeSpecifiedException
     */
    #[DataProvider('provideFieldKeyTestData')]
    public function testValidateFieldKeys(
        array $configData,
        string $moduleName,
        string $expectedExceptionClass,
        string $expectedExceptionMessage,
    ): void {
        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $this->validator->validateConfigOption($configData, $moduleName);
    }

    /**
     * @throws ConfigFieldTypeScalarMismatchException
     * @throws UnknownFieldSpecifiedException
     * @throws MissingConfigOptionSubfieldException
     * @throws ConfigFieldTypeInvalidPatternException
     * @throws UnknownFieldTypeSpecifiedException
     */
    public function testValidateFieldUnknownType(): void {
        $this->expectException(UnknownConfigFieldTypeSpecifiedException::class);
        $this->expectExceptionMessage("An unknown subfield type 'unknowntype' was discovered in the config option named 'testname' in module 'testmodule'");
        $this->validator->validateConfigOption(
            [
                'label' => 'testLabel',
                'type' => 'unknowntype',
                'configName' => 'testname'
            ],
            'testmodule'
        );
    }

    public static function provideInvalidFieldTypeScalarTestData(): iterable {
        yield 'invalid_config_name' => [
            'configData' => [
                'label' => 'testLabel',
                'type' => 'text',
                'configName' => 'testOName'
            ],
            'moduleName' => 'testmodule',
            'expectedExceptionClass' => ConfigFieldTypeInvalidPatternException::class,
            'expectedExceptionMessage' => "The field definition 'configName' in template 'testmodule' is invalid: Config name keys may only contain lowercase letters, numbers, and underscores."
        ];

        yield 'string_type_mismatch' => [
            'configData' => [
                'label' => false,
                'type' => 'text',
                'configName' => 'testname'
            ],
            'moduleName' => 'testmodule',
            'expectedExceptionClass' => ConfigFieldTypeScalarMismatchException::class,
            'expectedExceptionMessage' => "The field type 'label' defined in template 'testmodule' is invalid. Its expected type is string, its actual type is boolean"
        ];
    }

    #[DataProvider('provideInvalidFieldTypeScalarTestData')]
    public function testValidateFieldTypeCompliance(
        array $configData,
        string $moduleName,
        string $expectedExceptionClass,
        string $expectedExceptionMessage,
    ): void {
        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedExceptionMessage);
        $this->validator->validateConfigOption($configData, $moduleName);
    }
}
