<?php

declare(strict_types=1);

use App\Services\ManagedImages\Domain\ManagedImageDerivativeFormat;

test('construct', function () {
    $format = new ManagedImageDerivativeFormat(
        handle: 'thumbnail',
        resolutionX: 320,
        resolutionY: 180,
        quality: 85,
        id: 4,
    );

    self::assertSame('thumbnail', $format->getHandle());
    self::assertSame(320, $format->getResolutionX());
    self::assertSame(180, $format->getResolutionY());
    self::assertSame(85, $format->getQuality());
    self::assertSame(4, $format->getId());
});
