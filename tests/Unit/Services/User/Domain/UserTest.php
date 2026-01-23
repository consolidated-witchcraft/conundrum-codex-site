<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\User\Domain;

use App\Services\Files\Domain\ManagedFile;
use App\Services\User\Domain\Role;
use App\Services\User\Domain\User;
use App\Services\User\Domain\UserFeat;
use App\Services\User\Domain\ValueObjects\UserAccountType;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    private function generateTestUser(array $overrides = []): User {
        return new User(
            email: $overrides['email'] ?? 'test@test.local',
            username: $overrides['username'] ?? 'Test User',
            userAccountType: $overrides['userAccountType'] ?? $this->createMock(UserAccountType::class),
            password: $overrides['password'] ?? 'Test Pass',
            hasAcceptedCookies: $overrides['hasAcceptedCookies'] ?? false,
            profilePublic: $overrides['profilePublic'] ?? false,
            avatarUrl: $overrides['avatarUrl'] ?? null,
            avatar: $overrides['avatar'] ?? null,
            id: $overrides['id'] ?? null
        );
    }
    public function testConstruct(): void {
        $testEmail = 'test@test.local';
        $userName = 'Test User';
        $password = 'TestPass';
        $userAccountType = $this->createMock(UserAccountType::class);
        $id = 123;
        $user = new User(
            email: $testEmail,
            username: $userName,
            userAccountType: $userAccountType,
            password: $password,
            id: $id
        );
        self::assertEquals($testEmail, $user->getEmail());
        self::assertEquals($userName, $user->getUsername());
        self::assertEquals($testEmail, $user->getUserIdentifier());
        self::assertEquals($password, $user->getPassword());
        self::assertEquals($id, $user->getId());
        self::assertEquals($userAccountType, $user->getUserAccountType());
        self::assertFalse($user->getHasAcceptedCookies());
        self::assertFalse($user->getIsProfilePublic());
        self::assertNull($user->getAvatarUrl());
        self::assertEmpty($user->getRoles());
        self::assertEmpty($user->getGames());
        self::assertEmpty($user->getGamesMastered());
        self::assertEmpty($user->getArchivedGamesMastered());
        self::assertEmpty($user->getFeats());
        self::assertFalse($user->isBlocked());
        self::assertNull($user->getUserBlock());
    }

    public function testSetEmail(): void {
        $user = $this->generateTestUser();
        $newEmail = "new.email@test.com";
        self::assertNotEquals($newEmail, $user->getEmail());
        self::assertNotEquals($newEmail, $user->getUserIdentifier());
        $user->setEmail($newEmail);
        self::assertEquals($newEmail, $user->getEmail());
        self::assertEquals($newEmail, $user->getUserIdentifier());
    }

    public function testSetUsername(): void {
        $user = $this->generateTestUser();
        $newUsername = "new username";
        self::assertNotEquals($newUsername, $user->getUsername());
        $user->setUsername($newUsername);
        self::assertEquals($newUsername, $user->getUsername());
    }

    public function testSetPassword(): void {
        $user = $this->generateTestUser();
        $newPassword = "new password";
        self::assertNotEquals($newPassword, $user->getPassword());
        $user->setPassword($newPassword);
        self::assertEquals($newPassword, $user->getPassword());
    }

    public function testSetUserAccountType(): void {
        $user = $this->generateTestUser();
        $newUserAccountType = $this->createConfiguredMock(UserAccountType::class, [
            'getMaximumConcurrentGames' => 100
        ]);
        $user->setUserAccountType($newUserAccountType);
        self::assertEquals($newUserAccountType, $user->getUserAccountType());
    }

    public function testSetHasAcceptedCookies(): void {
        $user = $this->generateTestUser();
        self::assertFalse($user->getHasAcceptedCookies());
        $user->setHasAcceptedCookies(true);
        self::assertTrue($user->getHasAcceptedCookies());
    }

    public function testSetProfilePublic(): void {
        $user = $this->generateTestUser();
        self::assertFalse($user->getIsProfilePublic());
        $user->setIsProfilePublic(true);
        self::assertTrue($user->getIsProfilePublic());
    }

    public function testSetAvatarUrl(): void {
        $user = $this->generateTestUser();
        $testAvatarUrl = 'http://example.com/avatar.jpg';
        self::assertNull($user->getAvatarUrl());
        $user->setAvatarUrl($testAvatarUrl);
        self::assertEquals($testAvatarUrl, $user->getAvatarUrl());
    }

    public function testSetId(): void {
        $user = $this->generateTestUser();
        self::assertNull($user->getId());
        $user->setId(123);
        self::assertEquals(123, $user->getId());
    }

    public function testSetRoles(): void {
        $user = $this->generateTestUser();
        self::assertEmpty($user->getHydratedRoles());
        $roles = new ArrayCollection([
            $this->createConfiguredMock(
                Role::class,
                [
                    'getHandle' => 'ROLE_USER',
                ]
            ),
            $this->createConfiguredMock(
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
    }

    public function testAddRole(): void {
        $user = $this->generateTestUser();
        $roles = new ArrayCollection([
            $this->createConfiguredMock(
                Role::class,
                [
                    'getHandle' => 'ROLE_USER',
                ]
            ),
            $this->createConfiguredMock(
                Role::class,
                [
                    'getHandle' => 'ROLE_ADMIN',
                ]
            ),
        ]);
        $user->setRoles($roles);
        $newRole = $this->createConfiguredMock(Role::class, [
            'getHandle' => 'ROLE_MODERATOR',
        ]);
        $user->addRole($newRole);
        self::assertEquals([
            'ROLE_USER',
            'ROLE_ADMIN',
            'ROLE_MODERATOR',
        ], $user->getRoles());
    }

    public function testSetFeats(): void {
        $user = $this->generateTestUser();
        self::assertEmpty($user->getFeats());
        $feats = new ArrayCollection([
            $this->createConfiguredMock(UserFeat::class, ['getHandle' => 'FEAT_1']),
            $this->createConfiguredMock(UserFeat::class, ['getHandle' => 'FEAT_2'])
        ]);
        $user->setFeats($feats);
        self::assertEquals($feats, $user->getFeats());
    }

    public function testAwardFeat(): void {
        $user = $this->generateTestUser();
        self::assertEmpty($user->getFeats());
        $feat = $this->createConfiguredMock(UserFeat::class, ['getHandle' => 'FEAT_1']);
        $user->awardFeat($feat);
        self::assertEquals(new ArrayCollection([$feat]), $user->getFeats());
    }

    public function testCanCreateGames(): void {
        $userOne = $this->generateTestUser([
            'userAccountType' => $this->createConfiguredMock(UserAccountType::class, [
                'getMaximumConcurrentGames' => 1
            ])
        ]);
        self::assertTrue($userOne->canCreateGames());

        $userTwo = $this->generateTestUser([
            'userAccountType' => $this->createConfiguredMock(UserAccountType::class, [
                'getMaximumConcurrentGames' => 0
            ])
        ]);
        self::assertFalse($userTwo->canCreateGames());
    }

    public function testSetAvatar(): void {
        $user = $this->generateTestUser();
        $avatar = $this->createConfiguredMock(ManagedFile::class, [
            'getTitle' => 'Test Title'
        ]);
        self::assertNotEquals($avatar, $user->getAvatar());
        $user->setAvatar($avatar);
        self::assertEquals($avatar, $user->getAvatar());
    }
}
