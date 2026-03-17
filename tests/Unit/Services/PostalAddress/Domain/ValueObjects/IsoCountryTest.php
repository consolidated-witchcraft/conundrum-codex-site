<?php

declare(strict_types=1);

use App\Services\PostalAddress\Domain\ValueObjects\IsoCountry;

test('construct', function () {
    $isoCountry = new IsoCountry(
        isoCountryCode: 'GB',
        alphaThree: 'GBR',
        name: 'United Kingdom',
        num: 826,
    );

    self::assertSame('GB', $isoCountry->getIsoCountryCode());
    self::assertSame('GBR', $isoCountry->getAlphaThree());
    self::assertSame('United Kingdom', $isoCountry->getName());
    self::assertSame(826, $isoCountry->getNum());
    self::assertSame('United Kingdom', (string) $isoCountry);
});
