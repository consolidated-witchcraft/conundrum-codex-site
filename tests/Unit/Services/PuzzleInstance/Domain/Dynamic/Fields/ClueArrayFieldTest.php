<?php

declare(strict_types=1);

use App\Services\PuzzleInstance\Domain\Dynamic\Fields\ClueArrayField;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\DynamicFieldInvalidValueException;
use App\Services\PuzzleTemplate\Service\ValidPuzzleConfigOptionTypes;

test('construct', function () {
    $value = [['title' => 'Clue', 'body' => 'Body']];
    $field = new ClueArrayField('testName', 'testLabel', $value);

    self::assertSame('testName', $field->getName());
    self::assertSame('testLabel', $field->getLabel());
    self::assertSame($value, $field->getValue());
});

test('metadata and serialization', function () {
    $value = [['title' => 'Clue', 'body' => 'Body']];
    $field = new ClueArrayField('testName', 'testLabel', $value);

    self::assertSame(ValidPuzzleConfigOptionTypes::KEY_VALUE_LIST_TYPE_KEY, $field->getType());
    self::assertSame('array<string, string>', ClueArrayField::getAcceptedScalarTypeDescription());
    self::assertSame([
        'name' => 'testName',
        'label' => 'testLabel',
        'value' => $value,
        'type' => ValidPuzzleConfigOptionTypes::KEY_VALUE_LIST_TYPE_KEY,
    ], $field->jsonSerialize());
});

test('invalid values are rejected', function () {
    $this->expectException(DynamicFieldInvalidValueException::class);

    new ClueArrayField('testName', 'testLabel', ['not-an-array-item']);
});
