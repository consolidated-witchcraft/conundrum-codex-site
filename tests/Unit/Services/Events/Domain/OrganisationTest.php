<?php

declare(strict_types=1);

use App\Services\Events\Domain\Event;
use App\Services\Events\Domain\Organisation;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\PostalAddress\Domain\PostalAddress;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;

beforeEach(function () {
    $this->makeAddress = function (): PostalAddress {
        return new PostalAddress(
            user: $this->createStub(User::class),
            label: 'HQ',
            addressLineOne: '123 Main St',
            townCity: 'Testville',
            postalZip: 'AB12 3CD',
            countryCode: 'GB',
            countyProvince: 'Testshire',
        );
    };
});

test('construct', function () {
    $logo = $this->createStub(ManagedImage::class);
    $owner = $this->createStub(User::class);
    $events = new ArrayCollection([$this->createStub(Event::class)]);
    $managers = new ArrayCollection([$this->createStub(User::class)]);
    $postalAddress = ($this->makeAddress)();
    $invoiceAddress = ($this->makeAddress)();

    $organisation = new Organisation(
        name: 'Test Org',
        handle: 'test_org',
        organisationLogo: $logo,
        owner: $owner,
        events: $events,
        managers: $managers,
        postalAddress: $postalAddress,
        invoiceAddress: $invoiceAddress,
        id: 21,
    );

    self::assertSame('Test Org', $organisation->getName());
    self::assertSame('test_org', $organisation->getHandle());
    self::assertSame($logo, $organisation->getOrganisationLogo());
    self::assertSame($owner, $organisation->getOwner());
    self::assertSame($events, $organisation->getAllEvents());
    self::assertSame($managers, $organisation->getManagers());
    self::assertSame($postalAddress, $organisation->getPostalAddress());
    self::assertSame($invoiceAddress, $organisation->getInvoiceAddress());
    self::assertTrue($organisation->isActive());
    self::assertSame(21, $organisation->getId());
});

test('setters and deactivate update state', function () {
    $organisation = new Organisation(
        name: 'Test Org',
        handle: 'test_org',
        organisationLogo: $this->createStub(ManagedImage::class),
        owner: $this->createStub(User::class),
        events: new ArrayCollection(),
        managers: new ArrayCollection(),
        postalAddress: ($this->makeAddress)(),
        invoiceAddress: ($this->makeAddress)(),
    );
    $events = new ArrayCollection([$this->createStub(Event::class)]);
    $managers = new ArrayCollection([$this->createStub(User::class)]);
    $postalAddress = ($this->makeAddress)();
    $invoiceAddress = ($this->makeAddress)();
    $owner = $this->createStub(User::class);
    $logo = $this->createStub(ManagedImage::class);

    $organisation->setName('Updated Org');
    $organisation->setHandle('updated_org');
    $organisation->setOrganisationLogo($logo);
    $organisation->setOwner($owner);
    $organisation->setEvents($events);
    $organisation->setManagers($managers);
    $organisation->setPostalAddress($postalAddress);
    $organisation->setInvoiceAddress($invoiceAddress);
    $organisation->deactivate();

    self::assertSame('Updated Org', $organisation->getName());
    self::assertSame('updated_org', $organisation->getHandle());
    self::assertSame($logo, $organisation->getOrganisationLogo());
    self::assertSame($owner, $organisation->getOwner());
    self::assertSame($events, $organisation->getAllEvents());
    self::assertSame($managers, $organisation->getManagers());
    self::assertSame($postalAddress, $organisation->getPostalAddress());
    self::assertSame($invoiceAddress, $organisation->getInvoiceAddress());
    self::assertFalse($organisation->isActive());
    self::assertSame($events, $organisation->getUpcomingEvents());
});
