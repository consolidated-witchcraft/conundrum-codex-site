<?php

declare(strict_types=1);

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\KeyValueListField;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;

test('construct', function () {
    $value = [['foo' => 'bar'], ['baz' => 'qux']];
    $field = new KeyValueListField('testName', 'testLabel', $value);

    self::assertSame('testName', $field->getName());
    self::assertSame('testLabel', $field->getLabel());
    self::assertSame($value, $field->getValue());
});

test('metadata and serialization', function () {
    $value = [['foo' => 'bar']];
    $field = new KeyValueListField('testName', 'testLabel', $value);

    self::assertSame(ValidPuzzleConfigOptionTypes::KEY_VALUE_LIST_TYPE_KEY, $field->getType());
    self::assertSame('array<string, string>', KeyValueListField::getAcceptedScalarTypeDescription());
    self::assertSame([
        'name' => 'testName',
        'label' => 'testLabel',
        'value' => $value,
        'type' => ValidPuzzleConfigOptionTypes::KEY_VALUE_LIST_TYPE_KEY,
    ], $field->jsonSerialize());
});

test('value can be updated and cleared', function () {
    $field = new KeyValueListField('testName', 'testLabel');
    $value = [['foo' => 'bar']];

    $field->setValue($value);
    self::assertSame($value, $field->getValue());

    $field->clearValue();
    self::assertNull($field->getValue());
});

test('invalid values are rejected', function () {
    $this->expectException(DynamicFieldInvalidValueException::class);

    new KeyValueListField('testName', 'testLabel', 'invalid');
});
