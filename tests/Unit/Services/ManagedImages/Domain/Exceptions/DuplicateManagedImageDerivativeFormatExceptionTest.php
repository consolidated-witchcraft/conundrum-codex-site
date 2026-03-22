<?php

declare(strict_types=1);

use App\Services\ManagedImages\Domain\Exceptions\AbstractManagedImageDomainException;
use App\Services\ManagedImages\Domain\Exceptions\DuplicateManagedImageDerivativeFormatException;

test('construct', function () {
    $exception = new DuplicateManagedImageDerivativeFormatException('thumbnail');

    self::assertInstanceOf(AbstractManagedImageDomainException::class, $exception);
    self::assertSame(
        'A managed image derivative with format "thumbnail" already exists for this image.',
        $exception->getMessage()
    );
});
