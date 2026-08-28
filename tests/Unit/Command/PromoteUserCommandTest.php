<?php

declare(strict_types=1);

use App\Command\PromoteUserCommand;
use App\Services\User\Domain\Role;
use App\Services\User\Domain\User;
use App\Services\User\Service\UserService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;

beforeEach(function () {
    $this->userService = $this->createMock(UserService::class);
    $this->commandTester = new CommandTester(new PromoteUserCommand($this->userService));
});

test('command rejects an invalid email address', function () {
    $this->userService
        ->expects($this->never())
        ->method('loadUserByIdentifier');

    $exitCode = $this->commandTester->execute(['email' => 'not-an-email-address']);

    self::assertSame(Command::FAILURE, $exitCode);
    self::assertStringContainsString('Invalid email address.', $this->commandTester->getDisplay());
});

test('command reports when the user does not exist', function () {
    $this->userService
        ->expects($this->once())
        ->method('loadUserByIdentifier')
        ->with('missing@example.com')
        ->willThrowException(new UserNotFoundException());

    $exitCode = $this->commandTester->execute(['email' => 'missing@example.com']);

    self::assertSame(Command::FAILURE, $exitCode);
    self::assertStringContainsString(
        'No user with email "missing@example.com" found.',
        $this->commandTester->getDisplay(),
    );
});

test('command reports an account status error', function () {
    $this->userService
        ->expects($this->once())
        ->method('loadUserByIdentifier')
        ->with('blocked@example.com')
        ->willThrowException(new CustomUserMessageAccountStatusException('This account is blocked.'));

    $exitCode = $this->commandTester->execute(['email' => 'blocked@example.com']);

    self::assertSame(Command::FAILURE, $exitCode);
    self::assertStringContainsString('This account is blocked.', $this->commandTester->getDisplay());
});

test('command reports when the admin role does not exist', function () {
    $user = $this->createStub(User::class);

    $this->userService
        ->expects($this->once())
        ->method('loadUserByIdentifier')
        ->with('user@example.com')
        ->willReturn($user);
    $this->userService
        ->expects($this->once())
        ->method('getRoleByHandle')
        ->with('ROLE_ADMIN')
        ->willReturn(null);
    $this->userService
        ->expects($this->never())
        ->method('saveUser');

    $exitCode = $this->commandTester->execute(['email' => 'user@example.com']);

    self::assertSame(Command::FAILURE, $exitCode);
    self::assertStringContainsString('The admin role does not exist.', $this->commandTester->getDisplay());
});

test('command does not save a user who is already an admin', function () {
    $adminRole = $this->createStub(Role::class);
    $user = $this->createMock(User::class);
    $user
        ->expects($this->once())
        ->method('hasRole')
        ->with($adminRole)
        ->willReturn(true);
    $user
        ->expects($this->never())
        ->method('addRole');

    $this->userService
        ->method('loadUserByIdentifier')
        ->willReturn($user);
    $this->userService
        ->method('getRoleByHandle')
        ->with('ROLE_ADMIN')
        ->willReturn($adminRole);
    $this->userService
        ->expects($this->never())
        ->method('saveUser');

    $exitCode = $this->commandTester->execute(['email' => 'admin@example.com']);

    self::assertSame(Command::SUCCESS, $exitCode);
    self::assertStringContainsString(
        'The user "admin@example.com" is already promoted. Aborting',
        $this->commandTester->getDisplay(),
    );
});

test('command adds the admin role and saves the user', function () {
    $adminRole = $this->createStub(Role::class);
    $user = $this->createMock(User::class);
    $user
        ->expects($this->once())
        ->method('hasRole')
        ->with($adminRole)
        ->willReturn(false);
    $user
        ->expects($this->once())
        ->method('addRole')
        ->with($adminRole);

    $this->userService
        ->method('loadUserByIdentifier')
        ->with('user@example.com')
        ->willReturn($user);
    $this->userService
        ->method('getRoleByHandle')
        ->with('ROLE_ADMIN')
        ->willReturn($adminRole);
    $this->userService
        ->expects($this->once())
        ->method('saveUser')
        ->with($user)
        ->willReturn($user);

    $exitCode = $this->commandTester->execute(['email' => 'user@example.com']);

    self::assertSame(Command::SUCCESS, $exitCode);
    self::assertStringContainsString('User Promoted.', $this->commandTester->getDisplay());
});
