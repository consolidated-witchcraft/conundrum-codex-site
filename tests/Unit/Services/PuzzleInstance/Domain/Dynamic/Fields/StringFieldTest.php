<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\PuzzleInstance\Domain\Dynamic\Fields;

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldUnsafeValueException;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\StringField;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StringFieldTest extends TestCase
{
    public function testConstruct(): void {
        $stringField = new StringField(
            name: 'testName',
            label: 'testLabel',
            value: 'lorem ipsum',
        );
        self::assertInstanceOf(StringField::class, $stringField);
        self::assertEquals('testName', $stringField->getName());
        self::assertEquals('testLabel', $stringField->getLabel());
        self::assertEquals('lorem ipsum', $stringField->getValue());
    }

    public function testGetType(): void {
        $stringField = new StringField(
            name: 'testName',
            label: 'testLabel',
            value: null
        );
        self::assertEquals(
            ValidPuzzleConfigOptionTypes::STRING_TYPE_KEY,
            $stringField->getType()
        );
    }

    public function testGetAcceptedScalarTypeDescription(): void {
        self::assertEquals('string', StringField::getAcceptedScalarTypeDescription());
    }

    /**
     * @throws DynamicFieldInvalidValueException
     */
    public function testSetValueValid(): void {
        $stringField = new StringField(
            'testName',
            'testLabel',
            null
        );
        $value = 'Alpha Beta';
        $stringField->setValue($value);
        self::assertEquals($value, $stringField->getValue());
    }

    public function testClearValue(): void {
        $field = new StringField(
            'testName',
            'testLabel',
            null
        );

        $field->setValue('FooBarBaz');
        self::assertEquals('FooBarBaz', $field->getValue());
        $field->clearValue();
        self::assertNull($field->getValue());
    }

    public static function provideInvalidValues(): iterable {
        yield 'integer' => [
            'invalidValue' => 123
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
        new StringField(
            'testName',
            'testLabel',
            $invalidValue
        );
    }

    #[DataProvider('provideInvalidValues')]
    public function testSetInvalidValue(mixed $invalidValue): void {
        $field = new StringField(
            'testName',
            'testLabel',
            null
        );
        $this->expectException(DynamicFieldInvalidValueException::class);
        $field->setValue($invalidValue);
    }

    public function testGetValue(): void {
        $integerField = new StringField(
            'testName',
            'testLabel',
            null
        );
        self::assertNull($integerField->getValue());
    }

    public function testJsonSerialize(): void {
        $integerField = new StringField(
            'testName',
            'testLabel',
            'LoremIpsum'
        );

        $expectedJsonArray = [
            'name' => 'testName',
            'label' => 'testLabel',
            'value' => 'LoremIpsum',
            'type' => 'string'
        ];
        self::assertEquals($expectedJsonArray, $integerField->jsonSerialize());
    }

    public static function provideUnsafeInput(): iterable {
        yield 'at' => [
            'unsafeInput' => '@'
        ];
        yield 'url' => [
            'unsafeInput' => 'www.google.com'
        ];
        yield 'email' => [
            'unsafeInput' => 'test@test.com'
        ];
        yield 'ipV4' => [
            'unsafeInput' => '127.0.0.1'
        ];

        yield 'ipV6' => [
            'unsafeInput' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334'
        ];
    }

    #[DataProvider('provideUnsafeInput')]
    public function testUnsafeInput(string $unsafeInput): void {
        $this->expectException(DynamicFieldUnsafeValueException::class);
        new StringField(
            'testName',
            'testLabel',
            $unsafeInput
        );
    }
}
