<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\BooleanArrayField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\BooleanField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldValueMismatchException;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use function Symfony\Component\String\s;

class BooleanFieldTest extends TestCase
{
    public function testConstruct(): void {
        $booleanField = new BooleanField(
            name: 'testName',
            label: 'testLabel',
            value: true,
        );
        self::assertInstanceOf(BooleanField::class, $booleanField);
        self::assertEquals('testName', $booleanField->getName());
        self::assertEquals('testLabel', $booleanField->getLabel());
        self::assertTrue($booleanField->getValue());
    }

    public function testGetType(): void {
        $arrayField = new BooleanField(
            name: 'testName',
            label: 'testLabel',
            value: null
        );
        self::assertEquals(
            ValidPuzzleConfigOptionTypes::BOOLEAN_TYPE_KEY,
            $arrayField->getType()
        );
    }

    public function testGetAcceptedScalarTypeDescription(): void {
        self::assertEquals('bool', BooleanField::getAcceptedScalarTypeDescription());
    }

    public function testSetValueValid(): void {
        $arrayField = new BooleanField(
            'testName',
            'testLabel',
            null
        );
        $value = true;
        $arrayField->setValue($value);
        self::assertEquals($value, $arrayField->getValue());
    }

    public function testClearValue(): void {
        $arrayField = new BooleanField(
            'testName',
            'testLabel',
            null
        );

        $arrayField->setValue(true);
        self::assertTrue($arrayField->getValue());
        $arrayField->clearValue();
        self::assertNull($arrayField->getValue());
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
            'invalidValue' => []
        ];

        yield 'object' => [
            'invalidValue' => new \stdClass()
        ];
    }

    #[DataProvider('provideInvalidValues')]
    public function testValueInvalidOnConstruct(mixed $invalidValue): void {
        $this->expectException(DynamicFieldValueMismatchException::class);
        new BooleanField(
            'testName',
            'testLabel',
            $invalidValue
        );
    }

    #[DataProvider('provideInvalidValues')]
    public function testSetInvalidValue(mixed $invalidValue): void {
        $arrayField = new BooleanField(
            'testName',
            'testLabel',
            null
        );
        $this->expectException(DynamicFieldValueMismatchException::class);
        $arrayField->setValue($invalidValue);
    }

    public function testGetValue(): void {
        $arrayField = new BooleanField(
            'testName',
            'testLabel',
            null
        );
        self::assertNull($arrayField->getValue());
    }

    public function testJsonSerialize(): void {
        $arrayField = new BooleanField(
            'testName',
            'testLabel',
            true
        );

        $expectedJsonArray = [
            'name' => 'testName',
            'label' => 'testLabel',
            'value' => true,
            'type' => 'boolean'
        ];
        self::assertEquals($expectedJsonArray, $arrayField->jsonSerialize());
    }
}
