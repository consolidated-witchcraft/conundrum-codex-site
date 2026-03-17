<?php

declare(strict_types=1);

use App\Services\PuzzleTemplate\Domain\PuzzleCredit;

test('construct', function () {
    $puzzleCredit = new PuzzleCredit(
        name: 'Jane Doe',
        created: 'Writing',
        email: 'jane@example.com',
    );

    self::assertSame('Jane Doe', $puzzleCredit->name);
    self::assertSame('Writing', $puzzleCredit->created);
    self::assertSame('jane@example.com', $puzzleCredit->email);
});
