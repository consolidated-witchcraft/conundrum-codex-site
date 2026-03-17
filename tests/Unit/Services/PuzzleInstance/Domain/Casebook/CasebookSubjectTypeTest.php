<?php

declare(strict_types=1);

use App\Services\Core\Domain\Exceptions\InvalidHandleException;
use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectType;

test('construct', function () {
    $type = new CasebookSubjectType('Suspect', 'suspect', 4);

    self::assertSame('Suspect', $type->getLabel());
    self::assertSame('suspect', $type->getHandle());
    self::assertFalse(CasebookSubjectType::hasDescription());
    self::assertSame(4, $type->getId());
});

test('invalid handle is rejected', function () {
    $this->expectException(InvalidHandleException::class);

    new CasebookSubjectType('Suspect', 'suspect-type');
});
