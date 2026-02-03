<?php

declare(strict_types=1);

namespace App\Services\User\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Files\Domain\ManagedFile;
use App\Services\Game\Domain\Game;
use App\Services\User\Domain\ValueObjects\UserAccountType;
use App\Services\User\Infrastructure\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(
    repositoryClass: UserRepository::class
)]
#[ORM\Index(name: 'username_idx', columns: ['username'])]
#[UniqueEntity("email")]
class User extends AbstractDomainEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @var Collection<int, Game>
     */
    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: 'gamesMaster', orphanRemoval: true)]
    #[OrderBy(["createdAt" => "DESC"])]
    private Collection $gamesMastered;

    /**
     * @var Collection<int, Game>
     */
    #[ORM\ManyToMany(targetEntity: Game::class, mappedBy: 'players')]
    private Collection $games;

    /**
     * @var Collection<int, Role>
     */
    #[ORM\ManyToMany(targetEntity: Role::class, inversedBy: 'users', cascade: ['persist'], fetch: 'EAGER', indexBy: 'handle')]
    private Collection $roles;

    /**
     * @var Collection<int, UserFeat>
     */
    #[ORM\ManyToMany(targetEntity: UserFeat::class, cascade: ['persist'], fetch: 'EAGER', indexBy: 'handle')]
    private Collection $feats;

    /**
     * @param non-empty-string $email
     * @param non-empty-string $username
     * @param UserAccountType $userAccountType
     * @param non-empty-string $password
     * @param bool $hasAcceptedCookies
     * @param bool $profilePublic
     * @param ManagedFile|null $avatar
     * @param int|null $id
     */
    public function __construct(
        #[ORM\Column(type: 'email', length: 255, unique: true)]
        #[Assert\Email]
        private string $email,

        #[ORM\Column(type: 'non_empty_string', length: 255, nullable: false)]
        private string $username,

        #[ORM\ManyToOne(targetEntity: UserAccountType::class, fetch: 'EAGER')]
        #[ORM\JoinColumn(nullable: false)]
        private UserAccountType $userAccountType,

        #[ORM\OneToMany(targetEntity: UserBlock::class, cascade: ['persist'], mappedBy: 'user')]
        private readonly Collection $userBlocks,

        #[ORM\Column(type: 'non_empty_string', length: 255)]
        private string $password = 'REPLACEME',

        #[ORM\Column(type: 'boolean', nullable: false)]
        private bool $hasAcceptedCookies = false,

        #[ORM\Column(type: 'boolean', nullable: false)]
        private bool $profilePublic = false,

        #[ORM\OneToOne(targetEntity: ManagedFile::class, fetch: 'EAGER')]
        #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
        private ?ManagedFile $avatar = null,

        ? int $id = null
    ) {
        parent::__construct($id);
        $this->roles = new ArrayCollection();
        $this->gamesMastered = new ArrayCollection();
        $this->games = new ArrayCollection();
        $this->feats = new ArrayCollection();
    }

    public function getAvatar(): ?ManagedFile
    {
        return $this->avatar;
    }

    public function setAvatar(ManagedFile $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function isProfilePublic(): bool
    {
        return $this->profilePublic;
    }

    public function setProfilePublic(bool $profilePublic): void
    {
        $this->profilePublic = $profilePublic;
    }


    /**
     * @return non-empty-string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @return non-empty-string
     */
    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }


    /**
     * @param non-empty-string $email
     * @return void
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param non-empty-string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param non-empty-string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getUserAccountType(): UserAccountType
    {
        return $this->userAccountType;
    }

    public function setUserAccountType(UserAccountType $userAccountType): void {
        $this->userAccountType = $userAccountType;
    }

    public function getHasAcceptedCookies(): bool
    {
        return $this->hasAcceptedCookies;
    }

    public function setHasAcceptedCookies(bool $hasAcceptedCookies): void
    {
        $this->hasAcceptedCookies = $hasAcceptedCookies;
    }

    public function getIsProfilePublic(): bool
    {
        return $this->profilePublic;
    }

    public function setIsProfilePublic(bool $isProfilePublic): void
    {
        $this->profilePublic = $isProfilePublic;
    }


    /**
     * @return Collection<int, Game>
     */
    public function getGamesMastered(bool $includeArchived = false): Collection
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->isNull('archivedDate'));

        return $includeArchived ? $this->gamesMastered : $this->gamesMastered->matching($criteria);
    }

    /**
     * @return Collection<int, Game>
     */
    public function getArchivedGamesMastered(): Collection
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->isNotNull('archivedDate'));

        return $this->gamesMastered->matching($criteria);
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    /**
     * @see UserInterface
     * @return string[]
     */
    public function getRoles(): array
    {
        return array_map(function ($element) {
            return $element->getHandle();
        }, $this->roles->toArray());
    }

    /**
     * @return Collection<int, Role>
     */
    public function getHydratedRoles(): Collection {
        return $this->roles;
    }

    public function hasRole(Role $role): bool {
        return $this->roles->contains($role);
    }

    /**
     * @param Collection<int, Role> $roles
     */
    public function setRoles(Collection $roles): void
    {
        $this->roles = $roles;
    }

    public function addRole(Role $role): void {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }
    }

    /**
     * @return Collection<int, UserFeat>
     */
    public function getFeats(): Collection {
        return $this->feats;
    }

    /**
     * @param Collection<int, UserFeat> $feats
     */
    public function setFeats(Collection $feats): void {
        $this->feats = $feats;
    }
    public function awardFeat(UserFeat $userFeat): void {
        $this->feats->add($userFeat);
    }

    public function getUserBlock(): Collection {
        return $this->userBlocks;
    }


    public function isBlocked(): bool
    {
        return count($this->userBlocks) > 0;
    }

    public function canCreateGames(): bool
    {
        return count($this->getGamesMastered()) < $this->getUserAccountType()->getMaximumConcurrentGames();
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }
}
