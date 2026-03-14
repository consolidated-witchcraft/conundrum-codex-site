<?php

declare(strict_types=1);
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\BooleanArrayField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;
use PHPUnit\Framework\Attributes\DataProvider;
test('construct', function () {
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
});
test('get type', function () {
    $arrayField = new BooleanArrayField(
        name: 'testName',
        label: 'testLabel',
        value: null
    );
    self::assertEquals(
        ValidPuzzleConfigOptionTypes::BOOLEAN_ARRAY_TYPE_KEY,
        $arrayField->getType()
    );
});
test('get accepted scalar type description', function () {
    self::assertEquals('bool[]', BooleanArrayField::getAcceptedScalarTypeDescription());
});
test('set value valid', function () {
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
});
test('clear value', function () {
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
});
dataset('provideInvalidValues', function () {
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
});
test('value invalid on construct', function (mixed $invalidValue) {
    $this->expectException(DynamicFieldInvalidValueException::class);
    new BooleanArrayField(
        'testName',
        'testLabel',
        $invalidValue
    );
})->with('provideInvalidValues');
test('set invalid value', function (mixed $invalidValue) {
    $arrayField = new BooleanArrayField(
        'testName',
        'testLabel',
        null
    );
    $this->expectException(DynamicFieldInvalidValueException::class);
    $arrayField->setValue($invalidValue);
})->with('provideInvalidValues');
test('get value', function () {
    $arrayField = new BooleanArrayField(
        'testName',
        'testLabel',
        null
    );
    self::assertNull($arrayField->getValue());
});
test('json serialize', function () {
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
});
