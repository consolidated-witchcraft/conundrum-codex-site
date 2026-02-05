<?php

namespace App\Tests\Unit\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\BooleanField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\DieRollField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldValueMismatchException;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DieRollFieldTest extends TestCase
{
    public function testConstruct(): void {
        $dieRollField = new DieRollField(
            name: 'testName',
            label: 'testLabel',
            value: null,
        );
        self::assertInstanceOf(DieRollField::class, $dieRollField);
        self::assertEquals('testName', $dieRollField->getName());
        self::assertEquals('testLabel', $dieRollField->getLabel());
        self::assertNull($dieRollField->getValue());
    }

    public function testGetType(): void {
        $arrayField = new DieRollField(
            name: 'testName',
            label: 'testLabel',
            value: null
        );
        self::assertEquals(
            ValidPuzzleConfigOptionTypes::DIE_ROLL_TYPE_KEY,
            $arrayField->getType()
        );
    }

    public function testGetAcceptedScalarTypeDescription(): void {
        self::assertEquals("array{'x': int, 'd': int, 'p': ?int}", DieRollField::getAcceptedScalarTypeDescription());
    }

    public function testSetValueValid(): void {
        $dieRollField = new DieRollField(
            'testName',
            'testLabel',
            null
        );
        $value = [
            'x' => 10,
            'd' => 10,
            'p' => 4
        ];
        $dieRollField->setValue($value);
        self::assertEquals($value, $dieRollField->getValue());
    }

    public function testClearValue(): void {
        $dieRollField = new DieRollField(
            'testName',
            'testLabel',
            null
        );

        $value = [
            'x' => 10,
            'd' => 10,
            'p' => 4
        ];
        $dieRollField->setValue($value);
        self::assertEquals($value, $dieRollField->getValue());
        $dieRollField->clearValue();
        self::assertNull($dieRollField->getValue());
    }

    public static function provideInvalidValues(): iterable {
        yield 'string' => [
            'invalidValue' => 'HELLOWORLD'
        ];

        yield 'integer' => [
            'invalidValue' => 1
        ];

        yield 'float' => [
            'invalidValue' => 1.23
        ];

        yield 'array' => [
            'invalidValue' => [
                'lorem' => 'ipsum'
            ]
        ];

        yield 'object' => [
            'invalidValue' => new \stdClass()
        ];
    }

    #[DataProvider('provideInvalidValues')]
    public function testValueInvalidOnConstruct(mixed $invalidValue): void {
        $this->expectException(DynamicFieldValueMismatchException::class);
        new DieRollField(
            'testName',
            'testLabel',
            $invalidValue
        );
    }

    #[DataProvider('provideInvalidValues')]
    public function testSetInvalidValue(mixed $invalidValue): void {
        $arrayField = new DieRollField(
            'testName',
            'testLabel',
            null
        );
        $this->expectException(DynamicFieldValueMismatchException::class);
        $arrayField->setValue($invalidValue);
    }

    public function testGetValue(): void {
        $arrayField = new DieRollField(
            'testName',
            'testLabel',
            null
        );
        self::assertNull($arrayField->getValue());
    }

    public function testJsonSerialize(): void {
        $arrayField = new DieRollField(
            'testName',
            'testLabel',
            [
                'x' => 10,
                'd' => 10,
                'p' => 4
            ]
        );

        $expectedJsonArray = [
            'name' => 'testName',
            'label' => 'testLabel',
            'value' => [
                'x' => 10,
                'd' => 10,
                'p' => 4
            ],
            'type' => 'dieRoll'
        ];
        self::assertEquals($expectedJsonArray, $arrayField->jsonSerialize());
    }
}
