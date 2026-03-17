<?php

declare(strict_types=1);

use App\Services\Product\Domain\ProductVariant;
use App\Services\Ticket\Domain\TicketType;
use App\Services\Ticket\Domain\TicketTypeProductVariantMap;

test('construct and setters update state', function () {
    $ticketType = $this->createStub(TicketType::class);
    $productVariant = $this->createStub(ProductVariant::class);
    $updatedTicketType = $this->createStub(TicketType::class);
    $updatedProductVariant = $this->createStub(ProductVariant::class);

    $map = new TicketTypeProductVariantMap(
        ticketType: $ticketType,
        productVariant: $productVariant,
        id: 19,
    );

    self::assertSame($ticketType, $map->getTicketType());
    self::assertSame($productVariant, $map->getProductVariant());
    self::assertSame(19, $map->getId());

    $map->setTicketType($updatedTicketType);
    $map->setProductVariant($updatedProductVariant);

    self::assertSame($updatedTicketType, $map->getTicketType());
    self::assertSame($updatedProductVariant, $map->getProductVariant());
});
