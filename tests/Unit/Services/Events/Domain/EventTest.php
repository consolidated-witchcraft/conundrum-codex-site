<?php

declare(strict_types=1);

use App\Services\Events\Domain\Event;
use App\Services\Events\Domain\Organisation;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\PostalAddress\Domain\PostalAddress;
use App\Services\Ticket\Domain\TicketType;
use Doctrine\Common\Collections\ArrayCollection;

test('construct', function () {
    $heroImage = $this->createStub(ManagedImage::class);
    $location = $this->createStub(PostalAddress::class);
    $organisation = $this->createStub(Organisation::class);
    $startDate = new DateTimeImmutable('2026-05-01 10:00:00');
    $endDate = new DateTimeImmutable('2026-05-01 18:00:00');
    $ticketTypes = new ArrayCollection([$this->createStub(TicketType::class)]);

    $event = new Event(
        title: 'Test Event',
        eventSlug: 'test-event',
        description: 'A test event.',
        heroImage: $heroImage,
        location: $location,
        prebookOnly: true,
        organisation: $organisation,
        startDate: $startDate,
        endDate: $endDate,
        ticketTypes: $ticketTypes,
        id: 11,
    );

    self::assertSame('Test Event', $event->getTitle());
    self::assertSame('test-event', $event->getEventSlug());
    self::assertSame('A test event.', $event->getDescription());
    self::assertSame($heroImage, $event->getHeroImage());
    self::assertSame($location, $event->getLocation());
    self::assertTrue($event->isPrebookOnly());
    self::assertSame($organisation, $event->getOrganisation());
    self::assertSame($startDate, $event->getStartDate());
    self::assertSame($endDate, $event->getEndDate());
    self::assertSame($ticketTypes, $event->getTicketTypes());
    self::assertSame(11, $event->getId());
});

test('setters update state', function () {
    $event = new Event(
        title: 'Test Event',
        eventSlug: 'test-event',
        description: 'A test event.',
        heroImage: $this->createStub(ManagedImage::class),
        location: $this->createStub(PostalAddress::class),
        prebookOnly: true,
        organisation: $this->createStub(Organisation::class),
        startDate: new DateTimeImmutable('2026-05-01 10:00:00'),
        endDate: new DateTimeImmutable('2026-05-01 18:00:00'),
    );
    $heroImage = $this->createStub(ManagedImage::class);
    $location = $this->createStub(PostalAddress::class);
    $startDate = new DateTimeImmutable('2026-05-02 10:00:00');
    $endDate = new DateTimeImmutable('2026-05-02 18:00:00');
    $ticketTypes = new ArrayCollection([$this->createStub(TicketType::class)]);

    $event->setTitle('Updated Event');
    $event->setDescription('Updated description.');
    $event->setHeroImage($heroImage);
    $event->setLocation($location);
    $event->setPrebookOnly(false);
    $event->setStartDate($startDate);
    $event->setEndDate($endDate);
    $event->setTicketTypes($ticketTypes);

    self::assertSame('Updated Event', $event->getTitle());
    self::assertSame('Updated description.', $event->getDescription());
    self::assertSame($heroImage, $event->getHeroImage());
    self::assertSame($location, $event->getLocation());
    self::assertFalse($event->isPrebookOnly());
    self::assertSame($startDate, $event->getStartDate());
    self::assertSame($endDate, $event->getEndDate());
    self::assertSame($ticketTypes, $event->getTicketTypes());
});
