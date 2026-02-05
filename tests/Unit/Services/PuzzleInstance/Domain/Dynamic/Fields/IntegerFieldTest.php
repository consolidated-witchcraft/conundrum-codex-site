<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\BooleanArrayField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\BooleanField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\IntegerField;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use function Symfony\Component\String\s;

class IntegerFieldTest extends TestCase
{
    public function testConstruct(): void {
        $integerField = new IntegerField(
            name: 'testName',
            label: 'testLabel',
            value: 5,
        );
        self::assertInstanceOf(IntegerField::class, $integerField);
        self::assertEquals('testName', $integerField->getName());
        self::assertEquals('testLabel', $integerField->getLabel());
        self::assertEquals(5, $integerField->getValue());
    }

    public function testGetType(): void {
        $arrayField = new IntegerField(
            name: 'testName',
            label: 'testLabel',
            value: null
        );
        self::assertEquals(
            ValidPuzzleConfigOptionTypes::INTEGER_TYPE_KEY,
            $arrayField->getType()
        );
    }

    public function testGetAcceptedScalarTypeDescription(): void {
        self::assertEquals('int', IntegerField::getAcceptedScalarTypeDescription());
    }

    public function testSetValueValid(): void {
        $arrayField = new IntegerField(
            'testName',
            'testLabel',
            null
        );
        $value = 11;
        $arrayField->setValue($value);
        self::assertEquals($value, $arrayField->getValue());
    }

    public function testClearValue(): void {
        $arrayField = new IntegerField(
            'testName',
            'testLabel',
            null
        );

        $arrayField->setValue(11);
        self::assertEquals(11, $arrayField->getValue());
        $arrayField->clearValue();
        self::assertNull($arrayField->getValue());
    }

    public static function provideInvalidValues(): iterable {
        yield 'string' => [
            'invalidValue' => 'HELLOWORLD'
        ];

        yield 'boolean' => [
            'invalidValue' => true
        ];

        yield 'float' => [
            'invalidValue' => 1.23
        ];

        yield 'array' => [
            'invalidValue' => []
        ];

        yield 'object' => [
            'invalidValue' => new \stdClass()
        ];
    }

    #[DataProvider('provideInvalidValues')]
    public function testValueInvalidOnConstruct(mixed $invalidValue): void {
        $this->expectException(DynamicFieldInvalidValueException::class);
        new IntegerField(
            'testName',
            'testLabel',
            $invalidValue
        );
    }

    #[DataProvider('provideInvalidValues')]
    public function testSetInvalidValue(mixed $invalidValue): void {
        $arrayField = new IntegerField(
            'testName',
            'testLabel',
            null
        );
        $this->expectException(DynamicFieldInvalidValueException::class);
        $arrayField->setValue($invalidValue);
    }

    public function testGetValue(): void {
        $integerField = new IntegerField(
            'testName',
            'testLabel',
            null
        );
        self::assertNull($integerField->getValue());
    }

    public function testJsonSerialize(): void {
        $integerField = new IntegerField(
            'testName',
            'testLabel',
            15
        );

        $expectedJsonArray = [
            'name' => 'testName',
            'label' => 'testLabel',
            'value' => 15,
            'type' => 'integer'
        ];
        self::assertEquals($expectedJsonArray, $integerField->jsonSerialize());
    }
}
