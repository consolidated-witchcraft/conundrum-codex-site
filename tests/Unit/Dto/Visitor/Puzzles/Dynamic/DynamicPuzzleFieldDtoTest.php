<?php

declare(strict_types=1);

use App\Dto\Visitor\Puzzles\Dynamic\DynamicPuzzleFieldDto;

test('getValue preserves associative arrays', function () {
    $field = new DynamicPuzzleFieldDto(
        name: 'failure',
        type: 'dieRoll',
        value: [
            'x' => 1,
            'd' => 6,
            'p' => 0,
        ],
    );

    expect($field->getValue())->toBe([
        'x' => 1,
        'd' => 6,
        'p' => 0,
    ]);
});

test('getValue preserves list arrays as lists', function () {
    $field = new DynamicPuzzleFieldDto(
        name: 'buttons',
        type: 'stringArray',
        value: ['amber', 'azure', 'jade'],
    );

    expect($field->getValue())->toBe(['amber', 'azure', 'jade']);
});
