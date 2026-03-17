<?php

declare(strict_types=1);

use App\Services\Core\Domain\Exceptions\InvalidHandleException;
use App\Services\PuzzleTemplate\Domain\PuzzleCategory;

test('construct', function () {
    $puzzleCategory = new PuzzleCategory(
        label: 'Mystery',
        handle: 'mystery',
        description: 'Mystery puzzles.',
        id: 6,
    );

    self::assertSame('Mystery', $puzzleCategory->getLabel());
    self::assertSame('mystery', $puzzleCategory->getHandle());
    self::assertSame('Mystery puzzles.', $puzzleCategory->getDescription());
    self::assertTrue(PuzzleCategory::hasDescription());
    self::assertSame(6, $puzzleCategory->getId());
});

test('set description updates state', function () {
    $puzzleCategory = new PuzzleCategory(
        label: 'Mystery',
        handle: 'mystery',
        description: 'Mystery puzzles.',
    );

    $puzzleCategory->setDescription('Updated description.');

    self::assertSame('Updated description.', $puzzleCategory->getDescription());
});

test('invalid handle is rejected', function () {
    $this->expectException(InvalidHandleException::class);

    new PuzzleCategory(
        label: 'Mystery',
        handle: 'mystery-puzzles',
        description: 'Mystery puzzles.',
    );
});
