<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\BooleanArrayField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldValueMismatchException;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BooleanArrayFieldTest extends TestCase
{
    public function testConstruct(): void {
        $testValue = [
            false, true, true
        ];
        $arrayField = new BooleanArrayField(
            name: 'testName',
            label: 'testLabel',
            value: $testValue
        );
        self::assertInstanceOf(BooleanArrayField::class, $arrayField);
        self::assertEquals('testName', $arrayField->getName());
        self::assertEquals('testLabel', $arrayField->getLabel());
        self::assertEquals($testValue, $arrayField->getValue());
    }

    public function testGetType(): void {
        $arrayField = new BooleanArrayField(
            name: 'testName',
            label: 'testLabel',
            value: null
        );
        self::assertEquals(
            ValidPuzzleConfigOptionTypes::BOOLEAN_ARRAY_TYPE_KEY,
            $arrayField->getType()
        );
    }


    public function testGetAcceptedScalarTypeDescription(): void {
        self::assertEquals('bool[]', BooleanArrayField::getAcceptedScalarTypeDescription());
    }

    public function testSetValueValid(): void {
        $arrayField = new BooleanArrayField(
            'testName',
            'testLabel',
            null
        );
        $value = [
            true,
            false,
            true
        ];
        $arrayField->setValue($value);
        self::assertEquals($value, $arrayField->getValue());
    }

    public function testClearValue(): void {
        $arrayField = new BooleanArrayField(
            'testName',
            'testLabel',
            null
        );
        $arrayValue = [
            true, false, false
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

        yield 'array_of_non_booleans' => [
            'invalidValue' => [
                'lorem',
                123,
                false
            ]
        ];
    }

    #[DataProvider('provideInvalidValues')]
    public function testValueInvalidOnConstruct(mixed $invalidValue): void {
            $this->expectException(DynamicFieldValueMismatchException::class);
            new BooleanArrayField(
                'testName',
                'testLabel',
                $invalidValue
            );
    }

    #[DataProvider('provideInvalidValues')]
    public function testSetInvalidValue(mixed $invalidValue): void {
        $arrayField = new BooleanArrayField(
            'testName',
            'testLabel',
            null
        );
        $this->expectException(DynamicFieldValueMismatchException::class);
        $arrayField->setValue($invalidValue);
    }

    public function testGetValue(): void {
        $arrayField = new BooleanArrayField(
            'testName',
            'testLabel',
            null
        );
        self::assertNull($arrayField->getValue());
    }

    public function testJsonSerialize(): void {
        $arrayField = new BooleanArrayField(
            'testName',
            'testLabel',
            [false, true, true]
        );

        $expectedJsonArray = [
            'name' => 'testName',
            'label' => 'testLabel',
            'value' => [
                false,
                true,
                true
            ],
            'type' => 'booleanArray'
        ];
        self::assertEquals($expectedJsonArray, $arrayField->jsonSerialize());
    }
}
