<?php

declare(strict_types=1);

use App\Security\UserOwnsCharacterVoter;
use App\Services\Character\Domain\Character;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\User\Domain\Role;
use App\Services\User\Domain\User;
use App\Services\User\Domain\ValueObjects\UserAccountType;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

beforeEach(function () {
    $this->makeVoterUser = function (int $id): User {
        return new User(
            email: sprintf('user-%d@example.com', $id),
            username: sprintf('User %d', $id),
            userAccountType: $this->createStub(UserAccountType::class),
            id: $id,
        );
    };
    $this->makeVoterCharacter = function (User $owner): Character {
        return new Character(
            name: 'Seraphina Vale',
            user: $owner,
            class: 'Wizard',
            race: 'Human',
            portrait: $this->createStub(ManagedImage::class),
        );
    };
    $this->voteOnCharacter = function (mixed $user, Character $character): int {
        $token = $this->createConfiguredStub(TokenInterface::class, ['getUser' => $user]);

        return (new UserOwnsCharacterVoter())->vote(
            $token,
            $character,
            [UserOwnsCharacterVoter::MANAGE],
        );
    };
});

test('character owner can manage their character', function () {
    $owner = ($this->makeVoterUser)(id: 1);

    $result = ($this->voteOnCharacter)($owner, ($this->makeVoterCharacter)($owner));

    self::assertSame(expected: VoterInterface::ACCESS_GRANTED, actual: $result);
});

test('another user cannot manage the character', function () {
    $owner = ($this->makeVoterUser)(id: 1);
    $otherUser = ($this->makeVoterUser)(id: 2);

    $result = ($this->voteOnCharacter)($otherUser, ($this->makeVoterCharacter)($owner));

    self::assertSame(expected: VoterInterface::ACCESS_DENIED, actual: $result);
});

test('administrator can manage another users character', function () {
    $owner = ($this->makeVoterUser)(id: 1);
    $administrator = ($this->makeVoterUser)(id: 2);
    $administrator->addRole(new Role(label: 'Administrator', handle: 'ROLE_ADMIN'));

    $result = ($this->voteOnCharacter)($administrator, ($this->makeVoterCharacter)($owner));

    self::assertSame(expected: VoterInterface::ACCESS_GRANTED, actual: $result);
});

test('anonymous user cannot manage a character', function () {
    $owner = ($this->makeVoterUser)(id: 1);

    $result = ($this->voteOnCharacter)(null, ($this->makeVoterCharacter)($owner));

    self::assertSame(expected: VoterInterface::ACCESS_DENIED, actual: $result);
});
