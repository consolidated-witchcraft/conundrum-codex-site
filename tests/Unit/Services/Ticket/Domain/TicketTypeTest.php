<?php

declare(strict_types=1);

use App\Services\Events\Domain\Event;
use App\Services\Ticket\Domain\TicketType;
use App\Services\Ticket\Domain\TicketTypeProductVariantMap;
use Doctrine\Common\Collections\ArrayCollection;

test('construct', function () {
    $event = $this->createStub(Event::class);
    $maps = new ArrayCollection([$this->createStub(TicketTypeProductVariantMap::class)]);
    $refundableUntil = new DateTimeImmutable('2026-04-01 00:00:00');

    $ticketType = new TicketType(
        name: 'General Admission',
        slug: 'general-admission',
        description: 'General admission ticket.',
        priceCentessimal: 1500,
        event: $event,
        ticketTypeProductVariantMaps: $maps,
        refundableUntil: $refundableUntil,
        id: 27,
    );

    self::assertSame('General Admission', $ticketType->getName());
    self::assertSame('general-admission', $ticketType->getSlug());
    self::assertSame('General admission ticket.', $ticketType->getDescription());
    self::assertSame(1500, $ticketType->getPriceCentessimal());
    self::assertSame(15.0, $ticketType->getPriceDecimal());
    self::assertSame($event, $ticketType->getEvent());
    self::assertSame($maps, $ticketType->getTicketTypeProductVariantMaps());
    self::assertSame($refundableUntil, $ticketType->getRefundableUntil());
    self::assertSame(27, $ticketType->getId());
});

test('setters update state', function () {
    $ticketType = new TicketType(
        name: 'General Admission',
        slug: 'general-admission',
        description: 'General admission ticket.',
        priceCentessimal: 1500,
        event: $this->createStub(Event::class),
    );
    $maps = new ArrayCollection([$this->createStub(TicketTypeProductVariantMap::class)]);
    $refundableUntil = new DateTimeImmutable('2026-04-01 00:00:00');

    $ticketType->setName('VIP');
    $ticketType->setDescription('VIP ticket.');
    $ticketType->setPriceCentessimal(2500);
    $ticketType->setTicketTypeProductVariantMaps($maps);
    $ticketType->setRefundableUntil($refundableUntil);

    self::assertSame('VIP', $ticketType->getName());
    self::assertSame('VIP ticket.', $ticketType->getDescription());
    self::assertSame(2500, $ticketType->getPriceCentessimal());
    self::assertSame($maps, $ticketType->getTicketTypeProductVariantMaps());
    self::assertSame($refundableUntil, $ticketType->getRefundableUntil());
});
