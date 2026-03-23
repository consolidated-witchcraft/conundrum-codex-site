<?php

declare(strict_types=1);

use App\Services\ManagedImages\Domain\ManagedImageDerivativeFormat;

test('construct', function () {
    $format = new ManagedImageDerivativeFormat(
        handle: 'thumbnail',
        resolutionX: 320,
        resolutionY: null,
        quality: 85,
        allowCrop: true,
        centreImage: true,
        id: 4,
    );

    self::assertSame('thumbnail', $format->getHandle());
    self::assertSame(320, $format->getResolutionX());
    self::assertNull($format->getResolutionY());
    self::assertSame(85, $format->getQuality());
    self::assertTrue($format->isAllowCrop());
    self::assertTrue($format->isCentreImage());
    self::assertSame(4, $format->getId());
});
