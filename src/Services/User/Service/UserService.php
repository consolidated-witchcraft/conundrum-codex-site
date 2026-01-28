<?php

declare(strict_types=1);

namespace App\Services\User\Service;

use App\Entity\ResetPasswordRequest;
use App\Repository\ResetPasswordRequestRepository;
use App\Services\Core\Service\Interfaces\DomainServiceInterface;
use App\Services\Game\Domain\GameInvitation;
use App\Services\User\Domain\Permission;
use App\Services\User\Domain\Role;
use App\Services\User\Domain\User;
use App\Services\User\Domain\UserFeat;
use App\Services\User\Domain\ValueObjects\UserAccountType;
use App\Services\User\Infrastructure\Repository\PermissionRepository;
use App\Services\User\Infrastructure\Repository\RoleRepository;
use App\Services\User\Infrastructure\Repository\UserAccessTokenRepository;
use App\Services\User\Infrastructure\Repository\UserAccountTypeRepository;
use App\Services\User\Infrastructure\Repository\UserFeatRepository;
use App\Services\User\Infrastructure\Repository\UserRepository;
use App\Services\User\Service\Exceptions\MissingDefaultRoleException;
use App\Services\User\Service\Interfaces\UserServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @implements UserProviderInterface<User>
 */
class UserService
    implements UserProviderInterface, UserServiceInterface, AccessTokenHandlerInterface, DomainServiceInterface
{
    private const string DEFAULT_ROLE = 'ROLE_USER';
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PermissionRepository $permissionRepository,
        private readonly RoleRepository $roleRepository,
        private readonly UserAccessTokenRepository $userAccessTokenRepository,
        private readonly UserFeatRepository $userFeatRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
        private readonly ResetPasswordRequestRepository $resetPasswordRequestRepository,
        private readonly UserAccountTypeRepository $accountTypeRepository,
    ) {
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', $user::class));
        }
        $refreshedUser =  $this->userRepository->find($user->getId());
        if ($refreshedUser === null) {
            throw new UserNotFoundException();
        }
        return $refreshedUser;
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class || is_subclass_of($class, User::class);
    }

    /**
     * @param string $identifier
     * @throws UserNotFoundException
     * @throws CustomUserMessageAccountStatusException
     * @return UserInterface
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userRepository->findOneBy(['email' => $identifier]);
        if (!$user) {
            throw new UserNotFoundException();
        }

        if (null !== $user->getUserBlock()) {
            $block = $user->getUserBlock();

            $message = sprintf("Your account has been banned for %s. ", $block->getReason());
            $expired = false;
            if ($block->getExpirationDate()) {

                if ($block->getExpirationDate() < new \DateTime()) {
                    $expired = true;
                }
                $message .= sprintf("This ban will expire on %s", $block->getExpirationDate()->format('Y-m-d H:i'));
            } else {
                $message .= "This ban is permanent.";
            }
            if (false === $expired) {
                throw new CustomUserMessageAccountStatusException($message);
            }
        }

        return $user;
    }

    public function saveUser(User $user): User
    {
        return null === $user->getId() ? $this->createUser($user) : $this->updateUser($user);
    }

    /**
     * @throws MissingDefaultRoleException
     */
    private function createUser(User $user): User {
        if (count($user->getRoles()) === 0) {
            $role = $this->getRoleByHandle(self::DEFAULT_ROLE);
            if ($role === null) {
                throw new MissingDefaultRoleException(sprintf("Default role '%s' is not present in the database.", self::DEFAULT_ROLE));
            }
            $user->setRoles(new ArrayCollection([$role]));

        }
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    private function updateUser(User $user): User {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $user;
    }

    public function getRoleByHandle(string $roleHandle): ? Role {
        return $this->roleRepository->findOneBy(['handle' => $roleHandle]);
    }

    public function redeemInvitationForUser(GameInvitation $invitation, User $user): void
    {
        $invitation->setUser($user);
        $invitation->markUsed();
        $game = $invitation->getGame();
        $game->addPlayer($user);
        $this->entityManager->persist($invitation);
        $this->entityManager->persist($game);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function validateUser(User $user): ConstraintViolationListInterface
    {
        return $this->validator->validate($user);
    }

    public function getPermissionByHandle(string $permissionHandle): ?Permission
    {
        return $this->permissionRepository->findOneBy(['handle' => $permissionHandle]);
    }

    /**
     * @return User[]
     */
    public function getUsersPaginated(int $firstResult, int $maxResults = 50): iterable
    {
        return $this->userRepository->findAllPaginated($firstResult, $maxResults);
    }

    /**
     * @return User[]
     */
    public function findByEmailOrUserName(string $searchTerms, int $firstResult, int $maxResults = 50): iterable
    {
        return $this->userRepository->searchByEmailOrUserName($searchTerms, $firstResult, $maxResults);
    }

    public function getUsersCount(): int
    {
        return $this->userRepository->getUsersCount();
    }

    /**
     * @return Role[]
     */
    public function findAllRoles(): array
    {
        return $this->roleRepository->findAll();
    }

    public function getUserBadgeFrom(#[\SensitiveParameter] string $accessToken): UserBadge
    {
        $accessToken = $this->userAccessTokenRepository->findOneByToken($accessToken);
        if (null === $accessToken || $accessToken->isExpired()) {
            throw new BadCredentialsException('Invalid credentials.');
        }

        return new UserBadge($accessToken->getUserIdentifier());
    }

    /**
     * @return UserFeat[]
     */
    public function findAllFeats(): array
    {
        return $this->userFeatRepository->findAll();
    }

    /**
     * @return ResetPasswordRequest[]
     */
    public function getPasswordResetRequestsForUser(User $user): array {
        return $this->resetPasswordRequestRepository->findAllForUser($user);
    }

    public function getAccountTypeByHandle(string $handle):? UserAccountType {
        $accountType =  $this->accountTypeRepository->findByHandle($handle);
        return $accountType instanceof UserAccountType ? $accountType : null;
    }

    public function findById(int $id): ?User
    {
        return $this->userRepository->find($id);
    }

    /**
     * @return User[]
     */
    public function findUsersWithoutAccountType(): iterable
    {
        return $this->userRepository->findUsersWithoutAccountType();
    }

    /**
     * @return User[]
     */
    public function findAll(): iterable
    {
        return $this->userRepository->findAll();
    }
}
