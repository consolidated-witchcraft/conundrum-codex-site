<?php

declare(strict_types=1);
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\IntegerArrayField;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;
use PHPUnit\Framework\Attributes\DataProvider;
test('construct', function () {
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
});
test('get type', function () {
    $arrayField = new IntegerArrayField(
        name: 'testName',
        label: 'testLabel',
        value: null
    );
    self::assertEquals(
        ValidPuzzleConfigOptionTypes::INTEGER_ARRAY_TYPE_KEY,
        $arrayField->getType()
    );
});
test('get accepted scalar type description', function () {
    self::assertEquals('int[]', IntegerArrayField::getAcceptedScalarTypeDescription());
});
test('set value valid', function () {
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
});
test('clear value', function () {
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
});
dataset('provideInvalidValues', function () {
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
});
test('value invalid on construct', function (mixed $invalidValue) {
    $this->expectException(DynamicFieldInvalidValueException::class);
    new IntegerArrayField(
        'testName',
        'testLabel',
        $invalidValue
    );
})->with('provideInvalidValues');
test('set invalid value', function (mixed $invalidValue) {
    $arrayField = new IntegerArrayField(
        'testName',
        'testLabel',
        null
    );
    $this->expectException(DynamicFieldInvalidValueException::class);
    $arrayField->setValue($invalidValue);
})->with('provideInvalidValues');
test('get value', function () {
    $arrayField = new IntegerArrayField(
        'testName',
        'testLabel',
        null
    );
    self::assertNull($arrayField->getValue());
});
test('json serialize', function () {
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
});
