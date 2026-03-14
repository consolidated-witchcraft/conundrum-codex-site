<?php

declare(strict_types=1);
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldUnsafeValueException;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\StringField;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;
use PHPUnit\Framework\Attributes\DataProvider;
test('construct', function () {
    $stringField = new StringField(
        name: 'testName',
        label: 'testLabel',
        value: 'lorem ipsum',
    );
    self::assertInstanceOf(StringField::class, $stringField);
    self::assertEquals('testName', $stringField->getName());
    self::assertEquals('testLabel', $stringField->getLabel());
    self::assertEquals('lorem ipsum', $stringField->getValue());
});
test('get type', function () {
    $stringField = new StringField(
        name: 'testName',
        label: 'testLabel',
        value: null
    );
    self::assertEquals(
        ValidPuzzleConfigOptionTypes::STRING_TYPE_KEY,
        $stringField->getType()
    );
});
test('get accepted scalar type description', function () {
    self::assertEquals('string', StringField::getAcceptedScalarTypeDescription());
});
test('set value valid', function () {
    $stringField = new StringField(
        'testName',
        'testLabel',
        null
    );
    $value = 'Alpha Beta';
    $stringField->setValue($value);
    self::assertEquals($value, $stringField->getValue());
});
test('clear value', function () {
    $field = new StringField(
        'testName',
        'testLabel',
        null
    );

    $field->setValue('FooBarBaz');
    self::assertEquals('FooBarBaz', $field->getValue());
    $field->clearValue();
    self::assertNull($field->getValue());
});
dataset('provideInvalidValues', function () {
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
});
test('value invalid on construct', function (mixed $invalidValue) {
    $this->expectException(DynamicFieldInvalidValueException::class);
    new StringField(
        'testName',
        'testLabel',
        $invalidValue
    );
})->with('provideInvalidValues');
test('set invalid value', function (mixed $invalidValue) {
    $field = new StringField(
        'testName',
        'testLabel',
        null
    );
    $this->expectException(DynamicFieldInvalidValueException::class);
    $field->setValue($invalidValue);
})->with('provideInvalidValues');
test('get value', function () {
    $integerField = new StringField(
        'testName',
        'testLabel',
        null
    );
    self::assertNull($integerField->getValue());
});
test('json serialize', function () {
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
});
dataset('provideUnsafeInput', function () {
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
});
test('unsafe input', function (string $unsafeInput) {
    $this->expectException(DynamicFieldUnsafeValueException::class);
    new StringField(
        'testName',
        'testLabel',
        $unsafeInput
    );
})->with('provideUnsafeInput');
