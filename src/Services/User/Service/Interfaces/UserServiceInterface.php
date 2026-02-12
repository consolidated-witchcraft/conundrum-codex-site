<?php

namespace App\Services\User\Service\Interfaces;

use App\Services\Game\Domain\GameInvitation;
use App\Services\User\Domain\Permission;
use App\Services\User\Domain\Role;
use App\Services\User\Domain\User;
use App\Services\User\Domain\UserFeat;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

interface UserServiceInterface extends UserProviderInterface, UserPasswordHasherInterface
{
    public function saveUser(User $user): User;

    public function getRoleByHandle(string $roleHandle): ? Role;

    public function getPermissionByHandle(string $permissionHandle): ? Permission;

    public function redeemInvitationForUser(GameInvitation $invitation, User $user): void;

    public function validateUser(User $user): ConstraintViolationListInterface;

    /**
     * @return iterable<User>
     */
    public function getUsersPaginated(int $firstResult, int $maxResults = 50): iterable;

    /**
     * @return iterable<User>
     */
    public function findByEmailOrUserName(string $searchTerms, int $firstResult, int $maxResults = 50): iterable;

    public function getUsersCount(): int;

    /**
     * @return Role[]
     */
    public function findAllRoles(): array;

    /**
     * @return UserFeat[]
     */
    public function findAllFeats(): array;

    public function findById(int $id): ? User;

    /**
     * @return User[]
     */
    public function findUsersWithoutAccountType(): iterable;

    /**
     * @return User[]
     */
    public function findAll(): iterable;
}
