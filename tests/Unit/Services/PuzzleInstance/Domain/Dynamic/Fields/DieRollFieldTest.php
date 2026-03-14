<?php

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\BooleanField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\DieRollField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;
use PHPUnit\Framework\Attributes\DataProvider;

test('construct', function () {
    $dieRollField = new DieRollField(
        name: 'testName',
        label: 'testLabel',
        value: null,
    );
    self::assertInstanceOf(DieRollField::class, $dieRollField);
    self::assertEquals('testName', $dieRollField->getName());
    self::assertEquals('testLabel', $dieRollField->getLabel());
    self::assertNull($dieRollField->getValue());
});

test('get type', function () {
    $arrayField = new DieRollField(
        name: 'testName',
        label: 'testLabel',
        value: null
    );
    self::assertEquals(
        ValidPuzzleConfigOptionTypes::DIE_ROLL_TYPE_KEY,
        $arrayField->getType()
    );
});

test('get accepted scalar type description', function () {
    self::assertEquals("array{'x': int, 'd': int, 'p': ?int}", DieRollField::getAcceptedScalarTypeDescription());
});

test('set value valid', function () {
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
});

test('clear value', function () {
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
});

dataset('provideInvalidValues', function () {
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
});

test('value invalid on construct', function (mixed $invalidValue) {
    $this->expectException(DynamicFieldInvalidValueException::class);
    new DieRollField(
        'testName',
        'testLabel',
        $invalidValue
    );
})->with('provideInvalidValues');

test('set invalid value', function (mixed $invalidValue) {
    $arrayField = new DieRollField(
        'testName',
        'testLabel',
        null
    );
    $this->expectException(DynamicFieldInvalidValueException::class);
    $arrayField->setValue($invalidValue);
})->with('provideInvalidValues');

test('get value', function () {
    $arrayField = new DieRollField(
        'testName',
        'testLabel',
        null
    );
    self::assertNull($arrayField->getValue());
});

test('json serialize', function () {
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
});
