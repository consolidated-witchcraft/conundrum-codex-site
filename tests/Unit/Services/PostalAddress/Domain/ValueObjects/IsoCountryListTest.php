<?php

declare(strict_types=1);

use App\Services\PostalAddress\Domain\ValueObjects\IsoCountry;
use App\Services\PostalAddress\Domain\ValueObjects\IsoCountryList;

test('construct', function () {
    $countries = [
        new IsoCountry('GB', 'GBR', 'United Kingdom', 826),
        new IsoCountry('US', 'USA', 'United States', 840),
    ];

    $isoCountryList = new IsoCountryList($countries);

    self::assertSame($countries, $isoCountryList->getCountryList());
});
