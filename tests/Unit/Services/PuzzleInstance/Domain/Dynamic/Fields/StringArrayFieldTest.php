<?php

declare(strict_types=1);
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\IntegerArrayField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\StringArrayField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Traits\ValidatesStringsTrait;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;
use PHPUnit\Framework\Attributes\DataProvider;
test('construct', function () {
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
});
test('get type', function () {
    $arrayField = new StringArrayField(
        name: 'testName',
        label: 'testLabel',
        value: null
    );
    self::assertEquals(
        ValidPuzzleConfigOptionTypes::STRING_ARRAY_TYPE_KEY,
        $arrayField->getType()
    );
});
test('get accepted scalar type description', function () {
    self::assertEquals('string[]', StringArrayField::getAcceptedScalarTypeDescription());
});
test('set value valid', function () {
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
});
test('clear value', function () {
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
});
dataset('provideInvalidValues', function () {
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
});
test('value invalid on construct', function (mixed $invalidValue) {
    $this->expectException(DynamicFieldInvalidValueException::class);
    new StringArrayField(
        'testName',
        'testLabel',
        $invalidValue
    );
})->with('provideInvalidValues');
test('set invalid value', function (mixed $invalidValue) {
    $arrayField = new StringArrayField(
        'testName',
        'testLabel',
        null
    );
    $this->expectException(DynamicFieldInvalidValueException::class);
    $arrayField->setValue($invalidValue);
})->with('provideInvalidValues');
test('get value', function () {
    $arrayField = new StringArrayField(
        'testName',
        'testLabel',
        null
    );
    self::assertNull($arrayField->getValue());
});
test('json serialize', function () {
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
});
