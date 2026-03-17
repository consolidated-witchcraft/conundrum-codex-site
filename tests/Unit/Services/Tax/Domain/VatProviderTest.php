<?php

declare(strict_types=1);

use App\Services\Tax\Domain\VatProvider;

test('vat provider exposes rate metadata', function () {
    $vatProvider = new VatProvider();

    self::assertSame('V.A.T', $vatProvider->getTaxRateName());
    self::assertSame('vat', $vatProvider->getTaxRateHandle());
});

test('vat provider calculates tax centessimal', function () {
    $vatProvider = new VatProvider();

    self::assertSame(60, $vatProvider->getTaxCentessimal(100, 3));
});
