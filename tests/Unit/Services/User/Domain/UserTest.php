<?php

declare(strict_types=1);

use App\Services\Campaign\Domain\CampaignMembership;
use App\Services\Character\Domain\Character;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\User\Domain\Role;
use App\Services\User\Domain\User;
use App\Services\User\Domain\UserFeat;
use App\Services\User\Domain\ValueObjects\UserAccountType;
use Doctrine\Common\Collections\ArrayCollection;

beforeEach(function () {
    $this->makeUser = function (array $overrides = []): User {
        return new User(
            email: $overrides['email'] ?? 'test@test.local',
            username: $overrides['username'] ?? 'Test User',
            userAccountType: $overrides['userAccountType'] ?? $this->createStub(UserAccountType::class),
            userBlocks: $overrides['userBlocks'] ?? new ArrayCollection(),
            password: $overrides['password'] ?? 'Test Pass',
            avatar: $overrides['avatar'] ?? null,
            id: $overrides['id'] ?? null
        );
    };
});

test('construct', function () {
    $testEmail = 'test@test.local';
    $userName = 'Test User';
    $password = 'TestPass';
    $userAccountType = $this->createStub(UserAccountType::class);
    $id = 123;
    $user = new User(
        email: $testEmail,
        username: $userName,
        userAccountType: $userAccountType,
        password: $password,
        userBlocks: new ArrayCollection(),
        id: $id
    );
    self::assertEquals($testEmail, $user->getEmail());
    self::assertEquals($userName, $user->getUsername());
    self::assertEquals($testEmail, $user->getUserIdentifier());
    self::assertEquals($password, $user->getPassword());
    self::assertEquals($id, $user->getId());
    self::assertEquals($userAccountType, $user->getUserAccountType());
    self::assertFalse($user->getHasAcceptedCookies());
    self::assertFalse($user->isProfilePublic());
    self::assertEmpty($user->getRoles());
    self::assertEmpty($user->getGames());
    self::assertEmpty($user->getGamesMastered());
    self::assertEmpty($user->getArchivedGamesMastered());
    self::assertEmpty($user->getFeats());
    self::assertFalse($user->isBlocked());
    self::assertEmpty($user->getUserBlock());
    self::assertEmpty($user->getCampaignMemberships());
    self::assertEmpty($user->getCharacters());
});

test('add character adds it once and synchronises its user', function () {
    $user = ($this->makeUser)();
    $otherUser = ($this->makeUser)(['email' => 'other@test.local']);
    $character = $this->createMock(Character::class);
    $character->expects(self::once())->method('setUser')->with($user);
    $character->method('getUser')->willReturn($otherUser);

    $user->addCharacter(character: $character);
    $user->addCharacter(character: $character);

    self::assertSame(expected: [$character], actual: $user->getCharacters()->toArray());
});

test('remove character removes it', function () {
    $user = ($this->makeUser)();
    $character = $this->createStub(Character::class);
    $user->addCharacter(character: $character);

    $user->removeCharacter(character: $character);

    self::assertCount(expectedCount: 0, haystack: $user->getCharacters());
});

test('add campaign membership adds a membership once', function () {
    $user = ($this->makeUser)();
    $membership = $this->createStub(CampaignMembership::class);

    $user->addCampaignMembership(membership: $membership);
    $user->addCampaignMembership(membership: $membership);

    self::assertSame(expected: [$membership], actual: $user->getCampaignMemberships()->toArray());
});

test('remove campaign membership removes a membership', function () {
    $user = ($this->makeUser)();
    $membership = $this->createStub(CampaignMembership::class);
    $user->addCampaignMembership(membership: $membership);

    $user->removeCampaignMembership(membership: $membership);

    self::assertCount(expectedCount: 0, haystack: $user->getCampaignMemberships());
});

test('set email', function () {
    $user = ($this->makeUser)();
    $newEmail = "new.email@test.com";
    self::assertNotEquals($newEmail, $user->getEmail());
    self::assertNotEquals($newEmail, $user->getUserIdentifier());
    $user->setEmail($newEmail);
    self::assertEquals($newEmail, $user->getEmail());
    self::assertEquals($newEmail, $user->getUserIdentifier());
});

test('set username', function () {
    $user = ($this->makeUser)();
    $newUsername = "new username";
    self::assertNotEquals($newUsername, $user->getUsername());
    $user->setUsername($newUsername);
    self::assertEquals($newUsername, $user->getUsername());
});

test('set password', function () {
    $user = ($this->makeUser)();
    $newPassword = "new password";
    self::assertNotEquals($newPassword, $user->getPassword());
    $user->setPassword($newPassword);
    self::assertEquals($newPassword, $user->getPassword());
});

test('set user account type', function () {
    $user = ($this->makeUser)();
    $newUserAccountType = $this->createConfiguredStub(UserAccountType::class, [
        'getMaximumConcurrentGames' => 100
    ]);
    $user->setUserAccountType($newUserAccountType);
    self::assertEquals($newUserAccountType, $user->getUserAccountType());
});

test('set has accepted cookies', function () {
    $user = ($this->makeUser)();
    self::assertFalse($user->getHasAcceptedCookies());
    $user->setHasAcceptedCookies(true);
    self::assertTrue($user->getHasAcceptedCookies());
});

test('set profile public', function () {
    $user = ($this->makeUser)();
    self::assertFalse($user->isProfilePublic());
    $user->setIsProfilePublic(true);
    self::assertTrue($user->isProfilePublic());
});

test('set id', function () {
    $user = ($this->makeUser)();
    self::assertNull($user->getId());
    $user->setId(123);
    self::assertEquals(123, $user->getId());
});

test('set roles', function () {
    $user = ($this->makeUser)();
    self::assertEmpty($user->getHydratedRoles());
    $roles = new ArrayCollection([
        $this->createConfiguredStub(
            Role::class,
            [
                'getHandle' => 'ROLE_USER',
            ]
        ),
        $this->createConfiguredStub(
            Role::class,
            [
                'getHandle' => 'ROLE_ADMIN',
            ]
        ),
    ]);
    $user->setRoles($roles);
    self::assertEquals($roles, $user->getHydratedRoles());
    self::assertEquals([
        'ROLE_USER',
        'ROLE_ADMIN',
    ], $user->getRoles());
});

test('add role', function () {
    $user = ($this->makeUser)();
    $roles = new ArrayCollection([
        $this->createConfiguredStub(
            Role::class,
            [
                'getHandle' => 'ROLE_USER',
            ]
        ),
        $this->createConfiguredStub(
            Role::class,
            [
                'getHandle' => 'ROLE_ADMIN',
            ]
        ),
    ]);
    $user->setRoles($roles);
    $newRole = $this->createConfiguredStub(Role::class, [
        'getHandle' => 'ROLE_MODERATOR',
    ]);
    $user->addRole($newRole);
    self::assertEquals([
        'ROLE_USER',
        'ROLE_ADMIN',
        'ROLE_MODERATOR',
    ], $user->getRoles());
});

test('set feats', function () {
    $user = ($this->makeUser)();
    self::assertEmpty($user->getFeats());
    $feats = new ArrayCollection([
        $this->createConfiguredStub(UserFeat::class, ['getHandle' => 'FEAT_1']),
        $this->createConfiguredStub(UserFeat::class, ['getHandle' => 'FEAT_2'])
    ]);
    $user->setFeats($feats);
    self::assertEquals($feats, $user->getFeats());
});

test('award feat', function () {
    $user = ($this->makeUser)();
    self::assertEmpty($user->getFeats());
    $feat = $this->createConfiguredStub(UserFeat::class, ['getHandle' => 'FEAT_1']);
    $user->awardFeat($feat);
    self::assertEquals(new ArrayCollection([$feat]), $user->getFeats());
});

test('can create games', function () {
    $userOne = ($this->makeUser)([
        'userAccountType' => $this->createConfiguredStub(UserAccountType::class, [
            'getMaximumConcurrentGames' => 1
        ])
    ]);
    self::assertTrue($userOne->canCreateGames());

    $userTwo = ($this->makeUser)([
        'userAccountType' => $this->createConfiguredStub(UserAccountType::class, [
            'getMaximumConcurrentGames' => 0
        ])
    ]);
    self::assertFalse($userTwo->canCreateGames());
});

test('set avatar', function () {
    $user = ($this->makeUser)();
    $avatar = $this->createConfiguredStub(ManagedImage::class, [
        'getTitle' => 'Test Title'
    ]);
    self::assertNotEquals($avatar, $user->getAvatar());
    $user->setAvatar($avatar);
    self::assertEquals($avatar, $user->getAvatar());
});
