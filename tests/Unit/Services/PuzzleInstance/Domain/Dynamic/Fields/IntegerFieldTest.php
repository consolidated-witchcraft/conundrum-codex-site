<?php

declare(strict_types=1);
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\BooleanArrayField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\BooleanField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\IntegerField;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;
use PHPUnit\Framework\Attributes\DataProvider;
use function Symfony\Component\String\s;
test('construct', function () {
    $integerField = new IntegerField(
        name: 'testName',
        label: 'testLabel',
        value: 5,
    );
    self::assertInstanceOf(IntegerField::class, $integerField);
    self::assertEquals('testName', $integerField->getName());
    self::assertEquals('testLabel', $integerField->getLabel());
    self::assertEquals(5, $integerField->getValue());
});
test('get type', function () {
    $arrayField = new IntegerField(
        name: 'testName',
        label: 'testLabel',
        value: null
    );
    self::assertEquals(
        ValidPuzzleConfigOptionTypes::INTEGER_TYPE_KEY,
        $arrayField->getType()
    );
});
test('get accepted scalar type description', function () {
    self::assertEquals('int', IntegerField::getAcceptedScalarTypeDescription());
});
test('set value valid', function () {
    $arrayField = new IntegerField(
        'testName',
        'testLabel',
        null
    );
    $value = 11;
    $arrayField->setValue($value);
    self::assertEquals($value, $arrayField->getValue());
});
test('clear value', function () {
    $arrayField = new IntegerField(
        'testName',
        'testLabel',
        null
    );

    $arrayField->setValue(11);
    self::assertEquals(11, $arrayField->getValue());
    $arrayField->clearValue();
    self::assertNull($arrayField->getValue());
});
dataset('provideInvalidValues', function () {
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
});
test('value invalid on construct', function (mixed $invalidValue) {
    $this->expectException(DynamicFieldInvalidValueException::class);
    new IntegerField(
        'testName',
        'testLabel',
        $invalidValue
    );
})->with('provideInvalidValues');
test('set invalid value', function (mixed $invalidValue) {
    $arrayField = new IntegerField(
        'testName',
        'testLabel',
        null
    );
    $this->expectException(DynamicFieldInvalidValueException::class);
    $arrayField->setValue($invalidValue);
})->with('provideInvalidValues');
test('get value', function () {
    $integerField = new IntegerField(
        'testName',
        'testLabel',
        null
    );
    self::assertNull($integerField->getValue());
});
test('json serialize', function () {
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
});
