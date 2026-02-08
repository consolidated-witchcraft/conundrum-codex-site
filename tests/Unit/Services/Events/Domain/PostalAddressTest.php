<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\Events\Domain;

use App\Services\PostalAddress\Domain\PostalAddress;
use App\Services\User\Domain\User;
use PHPUnit\Framework\TestCase;

final class PostalAddressTest extends TestCase
{
    public function testConstructorSetsValues(): void
    {
        $user = $this->createStub(User::class);

        $address = new PostalAddress(
            'Home',
            '123 Main St',
            'Springfield',
            '90210',
            $user,
            'Unit B',
            40.7128,
            -74.0060,
            42
        );

        self::assertSame('Home', $address->getLabel());
        self::assertSame('123 Main St', $address->getAddressLineOne());
        self::assertSame('Springfield', $address->getTownCity());
        self::assertSame('90210', $address->getPostalZip());
        self::assertSame($user, $address->getUser());
        self::assertSame('Unit B', $address->getAddressLineTwo());
        self::assertSame(40.7128, $address->getLatitude());
        self::assertSame(-74.0060, $address->getLongitude());
        self::assertSame(42, $address->getId());
        self::assertInstanceOf(\DateTimeImmutable::class, $address->getCreatedAt());
        self::assertInstanceOf(\DateTimeImmutable::class, $address->getUpdatedAt());
    }

    public function testSettersUpdateValues(): void
    {
        $user = $this->createStub(User::class);

        $address = new PostalAddress(
            'Work',
            '1 First Ave',
            'Oldtown',
            '10000',
            $user
        );

        $address->setLabel('HQ');
        $address->setAddressLineOne('2 Second Ave');
        $address->setTownCity('Newtown');
        $address->setPostalZip('20000');
        $address->setAddressLineTwo('Suite 300');
        $address->setLatitude(51.5074);
        $address->setLongitude(-0.1278);

        self::assertSame('HQ', $address->getLabel());
        self::assertSame('2 Second Ave', $address->getAddressLineOne());
        self::assertSame('Newtown', $address->getTownCity());
        self::assertSame('20000', $address->getPostalZip());
        self::assertSame('Suite 300', $address->getAddressLineTwo());
        self::assertSame(51.5074, $address->getLatitude());
        self::assertSame(-0.1278, $address->getLongitude());
    }
}
