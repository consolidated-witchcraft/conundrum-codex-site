<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\User\Domain;

use App\Services\User\Domain\User;
use App\Services\User\Domain\UserAccessToken;
use PHPUnit\Framework\TestCase;
use Random\RandomException;

class UserAccessTokenTest extends TestCase
{
    /**
     * @throws \DateMalformedIntervalStringException
     * @throws RandomException
     */
    public function testMakeForUser(): void {
        $user = $this->createConfiguredStub(User::class, [
            'getUserIdentifier' => 'test@something.com'
        ]);
        $token = UserAccessToken::makeTokenForUser($user);
        self::assertEquals('test@something.com', $token->getUserIdentifier());
        self::assertEquals(128, strlen($token->getToken()));
        self::assertFalse($token->isExpired());
    }
}
