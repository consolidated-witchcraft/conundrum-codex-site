<?php

declare(strict_types=1);

use App\Services\PuzzleTemplate\Domain\InstanceConfigDto;

test('construct', function () {
    self::assertInstanceOf(InstanceConfigDto::class, new InstanceConfigDto());
});
