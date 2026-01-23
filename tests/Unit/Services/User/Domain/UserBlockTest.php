<?php

declare(strict_types=1);


namespace App\Tests\Unit\Services\User\Domain;

use App\Services\User\Domain\User;
use App\Services\User\Domain\UserBlock;
use PHPUnit\Framework\TestCase;
class UserBlockTest extends TestCase
{

    private function generateTestUserBlock(array $overrides = []): UserBlock {
        return new UserBlock(
            user: $overrides['user'] ?? $this->createStub(User::class),
            reason: $overrides['reason'] ?? 'Test Reason',
            expirationDate: $overrides['expirationDate'] ?? new \DateTimeImmutable('2100-01-01 00:00:00'),
            id: $overrides['id'] ?? null
        );
    }
    public function testConstruct(): void {
        $reason = 'test reason';
        $user = $this->createStub(User::class);
        $expirationDate = new \DateTimeImmutable('2100-01-01 00:00:00');
        $id = 123;
        $userBlock = new UserBlock(
            user: $user,
            reason: $reason,
            expirationDate: $expirationDate,
            id: $id
        );

        self::assertSame($user, $userBlock->getUser());
        self::assertSame($reason, $userBlock->getReason());
        self::assertSame($expirationDate, $userBlock->getExpirationDate());
        self::assertSame($id, $userBlock->getId());
    }

    public function testSetReason(): void {
        $userBlock = $this->generateTestUserBlock();
        $reason = 'test reason 2';
        self::assertNotEquals($reason, $userBlock->getReason());
        $userBlock->setReason($reason);
        self::assertSame($reason, $userBlock->getReason());
    }

    public function testSetExpirationDate(): void {
        $userBlock = $this->generateTestUserBlock();
        $expirationDate = new \DateTimeImmutable('2200-01-01 00:00:00');
        self::assertNotEquals($expirationDate, $userBlock->getExpirationDate());
        $userBlock->setExpirationDate($expirationDate);
        self::assertSame($expirationDate, $userBlock->getExpirationDate());
    }

    public function testIsPermanent(): void {
        $userBlock = $this->generateTestUserBlock();
        self::assertFalse($userBlock->isPermanent());

        $userBlock->setExpirationDate(null);
        self::assertTrue($userBlock->isPermanent());
    }

    public function testIsExpired(): void {
        $userBlock = $this->generateTestUserBlock([
            'expirationDate' => new \DateTimeImmutable('3000-01-01 00:00:00'),
        ]);
        self::assertFalse($userBlock->isExpired());
        $userBlock->setExpirationDate(new \DateTimeImmutable('2000-01-01 00:00:00'));
        self::assertTrue($userBlock->isExpired());
        $userBlock->setExpirationDate(null);
        self::assertFalse($userBlock->isExpired());
    }
}
