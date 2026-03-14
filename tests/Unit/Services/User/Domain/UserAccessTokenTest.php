<?php

declare(strict_types=1);
use App\Services\User\Domain\User;
use App\Services\User\Domain\UserAccessToken;
use Random\RandomException;
test('make for user', function () {
    $user = $this->createConfiguredStub(User::class, [
        'getUserIdentifier' => 'test@something.com'
    ]);
    $token = UserAccessToken::makeTokenForUser($user);
    self::assertEquals('test@something.com', $token->getUserIdentifier());
    self::assertEquals(128, strlen($token->getToken()));
    self::assertFalse($token->isExpired());
});
