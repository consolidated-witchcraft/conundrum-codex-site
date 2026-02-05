<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\IntegerArrayField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\StringArrayField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Traits\ValidatesStringsTrait;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StringArrayFieldTest extends TestCase
{
    public function testConstruct(): void {
        $testValue = [
            'lorem',
            'ipsum',
            'dolor'
        ];
        $arrayField = new StringArrayField(
            name: 'testName',
            label: 'testLabel',
            value: $testValue
        );
        self::assertInstanceOf(StringArrayField::class, $arrayField);
        self::assertEquals('testName', $arrayField->getName());
        self::assertEquals('testLabel', $arrayField->getLabel());
        self::assertEquals($testValue, $arrayField->getValue());
    }

    public function testGetType(): void {
        $arrayField = new StringArrayField(
            name: 'testName',
            label: 'testLabel',
            value: null
        );
        self::assertEquals(
            ValidPuzzleConfigOptionTypes::STRING_ARRAY_TYPE_KEY,
            $arrayField->getType()
        );
    }


    public function testGetAcceptedScalarTypeDescription(): void {
        self::assertEquals('string[]', StringArrayField::getAcceptedScalarTypeDescription());
    }

    public function testSetValueValid(): void {
        $arrayField = new StringArrayField(
            'testName',
            'testLabel',
            null
        );
        $value = [
            'alpha',
            'beta',
            'gamma'
        ];
        $arrayField->setValue($value);
        self::assertEquals($value, $arrayField->getValue());
    }

    public function testClearValue(): void {
        $arrayField = new StringArrayField(
            'testName',
            'testLabel',
            null
        );
        $arrayValue = [
            'foo',
            'bar',
            'baz'
        ];
        $arrayField->setValue($arrayValue);
        self::assertEquals($arrayValue, $arrayField->getValue());
        $arrayField->clearValue();
        self::assertNull($arrayField->getValue());
    }

    public static function provideInvalidValues(): iterable {
        yield 'not_array' => [
            'invalidValue' => 'HELLOWORLD'
        ];

        yield 'array_of_non_strings' => [
            'invalidValue' => [
                false,
                'string',
                8
            ]
        ];
    }

    #[DataProvider('provideInvalidValues')]
    public function testValueInvalidOnConstruct(mixed $invalidValue): void {
            $this->expectException(DynamicFieldInvalidValueException::class);
            new StringArrayField(
                'testName',
                'testLabel',
                $invalidValue
            );
    }

    #[DataProvider('provideInvalidValues')]
    public function testSetInvalidValue(mixed $invalidValue): void {
        $arrayField = new StringArrayField(
            'testName',
            'testLabel',
            null
        );
        $this->expectException(DynamicFieldInvalidValueException::class);
        $arrayField->setValue($invalidValue);
    }

    public function testGetValue(): void {
        $arrayField = new StringArrayField(
            'testName',
            'testLabel',
            null
        );
        self::assertNull($arrayField->getValue());
    }

    public function testJsonSerialize(): void {
        $arrayField = new StringArrayField(
            'testName',
            'testLabel',
            [
                'foo',
                'bar',
                'baz'
            ]
        );

        $expectedJsonArray = [
            'name' => 'testName',
            'label' => 'testLabel',
            'value' => [
                'foo',
                'bar',
                'baz'
            ],
            'type' => 'stringArray'
        ];
        self::assertEquals($expectedJsonArray, $arrayField->jsonSerialize());
    }
}
