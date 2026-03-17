<?php

declare(strict_types=1);

use App\Services\Core\Domain\Exceptions\InvalidHandleException;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectClueType;

test('construct', function () {
    $type = new CasebookSubjectClueType('Lead', 'lead', 3);

    self::assertSame('Lead', $type->getLabel());
    self::assertSame('lead', $type->getHandle());
    self::assertFalse(CasebookSubjectClueType::hasDescription());
    self::assertSame(3, $type->getId());
});

test('invalid handle is rejected', function () {
    $this->expectException(InvalidHandleException::class);

    new CasebookSubjectClueType('Lead', 'lead-type');
});
