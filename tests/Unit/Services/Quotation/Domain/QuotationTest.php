<?php

declare(strict_types=1);

use App\Services\Quotation\Domain\Quotation;

test('construct', function () {
    $quotation = new Quotation(
        quotation: 'Not all those who wander are lost.',
        citation: 'J.R.R. Tolkien',
        id: 3,
    );

    self::assertSame('Not all those who wander are lost.', $quotation->getQuotation());
    self::assertSame('J.R.R. Tolkien', $quotation->getCitation());
    self::assertSame(3, $quotation->getId());
});

test('setters update state', function () {
    $quotation = new Quotation(
        quotation: 'Original quotation',
        citation: 'Original citation',
    );

    $quotation->setQuotation('Updated quotation');
    $quotation->setCitation('Updated citation');

    self::assertSame('Updated quotation', $quotation->getQuotation());
    self::assertSame('Updated citation', $quotation->getCitation());
});
