<?php

declare(strict_types=1);
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\BooleanArrayField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\BooleanField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;
use PHPUnit\Framework\Attributes\DataProvider;
use function Symfony\Component\String\s;
test('construct', function () {
    $booleanField = new BooleanField(
        name: 'testName',
        label: 'testLabel',
        value: true,
    );
    self::assertInstanceOf(BooleanField::class, $booleanField);
    self::assertEquals('testName', $booleanField->getName());
    self::assertEquals('testLabel', $booleanField->getLabel());
    self::assertTrue($booleanField->getValue());
});
test('get type', function () {
    $arrayField = new BooleanField(
        name: 'testName',
        label: 'testLabel',
        value: null
    );
    self::assertEquals(
        ValidPuzzleConfigOptionTypes::BOOLEAN_TYPE_KEY,
        $arrayField->getType()
    );
});
test('get accepted scalar type description', function () {
    self::assertEquals('bool', BooleanField::getAcceptedScalarTypeDescription());
});
test('set value valid', function () {
    $arrayField = new BooleanField(
        'testName',
        'testLabel',
        null
    );
    $value = true;
    $arrayField->setValue($value);
    self::assertEquals($value, $arrayField->getValue());
});
test('clear value', function () {
    $arrayField = new BooleanField(
        'testName',
        'testLabel',
        null
    );

    $arrayField->setValue(true);
    self::assertTrue($arrayField->getValue());
    $arrayField->clearValue();
    self::assertNull($arrayField->getValue());
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
        'invalidValue' => []
    ];

    yield 'object' => [
        'invalidValue' => new \stdClass()
    ];
});
test('value invalid on construct', function (mixed $invalidValue) {
    $this->expectException(DynamicFieldInvalidValueException::class);
    new BooleanField(
        'testName',
        'testLabel',
        $invalidValue
    );
})->with('provideInvalidValues');
test('set invalid value', function (mixed $invalidValue) {
    $arrayField = new BooleanField(
        'testName',
        'testLabel',
        null
    );
    $this->expectException(DynamicFieldInvalidValueException::class);
    $arrayField->setValue($invalidValue);
})->with('provideInvalidValues');
test('get value', function () {
    $arrayField = new BooleanField(
        'testName',
        'testLabel',
        null
    );
    self::assertNull($arrayField->getValue());
});
test('json serialize', function () {
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
});
