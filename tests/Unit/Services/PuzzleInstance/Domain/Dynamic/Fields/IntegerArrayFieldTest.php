<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\IntegerArrayField;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class IntegerArrayFieldTest extends TestCase
{
    public function testConstruct(): void {
        $testValue = [
            3,7,11
        ];
        $arrayField = new IntegerArrayField(
            name: 'testName',
            label: 'testLabel',
            value: $testValue
        );
        self::assertInstanceOf(IntegerArrayField::class, $arrayField);
        self::assertEquals('testName', $arrayField->getName());
        self::assertEquals('testLabel', $arrayField->getLabel());
        self::assertEquals($testValue, $arrayField->getValue());
    }

    public function testGetType(): void {
        $arrayField = new IntegerArrayField(
            name: 'testName',
            label: 'testLabel',
            value: null
        );
        self::assertEquals(
            ValidPuzzleConfigOptionTypes::INTEGER_ARRAY_TYPE_KEY,
            $arrayField->getType()
        );
    }


    public function testGetAcceptedScalarTypeDescription(): void {
        self::assertEquals('int[]', IntegerArrayField::getAcceptedScalarTypeDescription());
    }

    public function testSetValueValid(): void {
        $arrayField = new IntegerArrayField(
            'testName',
            'testLabel',
            null
        );
        $value = [
            3,
            6,
            9
        ];
        $arrayField->setValue($value);
        self::assertEquals($value, $arrayField->getValue());
    }

    public function testClearValue(): void {
        $arrayField = new IntegerArrayField(
            'testName',
            'testLabel',
            null
        );
        $arrayValue = [
            5, 10, 15
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

        yield 'array_of_non_integers' => [
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
            new IntegerArrayField(
                'testName',
                'testLabel',
                $invalidValue
            );
    }

    #[DataProvider('provideInvalidValues')]
    public function testSetInvalidValue(mixed $invalidValue): void {
        $arrayField = new IntegerArrayField(
            'testName',
            'testLabel',
            null
        );
        $this->expectException(DynamicFieldInvalidValueException::class);
        $arrayField->setValue($invalidValue);
    }

    public function testGetValue(): void {
        $arrayField = new IntegerArrayField(
            'testName',
            'testLabel',
            null
        );
        self::assertNull($arrayField->getValue());
    }

    public function testJsonSerialize(): void {
        $arrayField = new IntegerArrayField(
            'testName',
            'testLabel',
            [2, 4, 8]
        );

        $expectedJsonArray = [
            'name' => 'testName',
            'label' => 'testLabel',
            'value' => [
                2,
                4,
                8
            ],
            'type' => 'integerArray'
        ];
        self::assertEquals($expectedJsonArray, $arrayField->jsonSerialize());
    }
}
