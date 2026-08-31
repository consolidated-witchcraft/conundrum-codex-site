<?php

declare(strict_types=1);

namespace App\Services\User\Domain;

use App\Services\Campaign\Domain\CampaignMembership;
use App\Services\Character\Domain\Character;
use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Events\Domain\Organisation;
use App\Services\Game\Domain\Game;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\Orders\Domain\Order;
use App\Services\PostalAddress\Domain\PostalAddress;
use App\Services\User\Domain\UserCart\UserCart;
use App\Services\User\Domain\ValueObjects\UserAccountType;
use App\Services\User\Infrastructure\Repository\UserRepository;
use App\Services\World\Domain\World;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OrderBy;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(
    repositoryClass: UserRepository::class
)]
#[ORM\Table(name: '`user`')]
#[ORM\Index(name: 'username_idx', columns: ['username'])]
#[UniqueEntity("email")]
class User extends AbstractDomainEntity implements UserInterface, PasswordAuthenticatedUserInterface
{
    /** @var Collection<int, Character> */
    #[ORM\OneToMany(targetEntity: Character::class, mappedBy: 'user', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $characters;

    /**
     * @var Collection<int, Game>
     */
    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: 'gamesMaster', orphanRemoval: true)]
    #[OrderBy(["createdAt" => "DESC"])]
    private Collection $gamesMastered;


    /**
     * @var Collection<int, World>
     */
    #[ORM\OneToMany(targetEntity: World::class, mappedBy: 'creator', orphanRemoval: true)]
    #[OrderBy(["createdAt" => "DESC"])]
    private Collection $worldsCreated;


    /**
     * @var Collection<int, World>
     */
    #[ORM\ManyToMany(targetEntity: World::class, inversedBy: 'players')]
    private Collection $worldsPlayed;

    /** @return Collection<int, World> */
    public function getWorldsPlayed(): Collection
    {
        return $this->worldsPlayed;
    }

    /** @var Collection<int, PostalAddress> */
    #[ORM\OneToMany(targetEntity: PostalAddress::class, mappedBy: 'user', orphanRemoval: true, fetch: 'EAGER')]
    private Collection $postalAddresses;

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

    /** @var Collection<int, Order> */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'user', orphanRemoval: false)]
    #[OrderBy(["createdAt" => "DESC"])]
    private Collection $orders;

    /** @var Collection<int, UserBlock> */
    #[ORM\OneToMany(targetEntity: UserBlock::class, mappedBy: 'user', cascade: ['persist'])]
    private Collection $userBlocks;


    /**
     * @param non-empty-string $email
     * @param non-empty-string $username
     * @param UserAccountType $userAccountType
     * @param non-empty-string $password
     * @param Collection<int, UserBlock>|null $userBlocks
     * @param UserCart|null $userCart
     * @param Organisation|null $organisation
     * @param ManagedImage|null $avatar
     * @param UserPreferences|null $userPreferences
     * @param Collection<int, CampaignMembership> $campaignMemberships
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

        #[ORM\Column(type: 'non_empty_string', length: 255)]
        private string $password = 'REPLACEME',

        ?Collection $userBlocks = null,

        #[ORM\OneToOne(targetEntity: UserCart::class, mappedBy: 'user', cascade: ['persist', 'refresh'], orphanRemoval: true)]
        private ? UserCart $userCart = null,

        #[ORM\ManyToOne(targetEntity: Organisation::class, fetch: 'EAGER', inversedBy: 'managers')]
        private ? Organisation $organisation = null,


        #[ORM\OneToOne(targetEntity: ManagedImage::class, fetch: 'EAGER')]
        #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
        private ?ManagedImage $avatar = null,

        #[ORM\Embedded(class: UserPreferences::class, columnPrefix: 'preferences_')]
        private ? UserPreferences $userPreferences = null,

        #[ORM\OneToMany(
            targetEntity: CampaignMembership::class,
            mappedBy: 'user',
            cascade: ['persist', 'remove'],
            orphanRemoval: true,
        )]
        private Collection $campaignMemberships = new ArrayCollection(),

        ? int $id = null
    ) {
        parent::__construct($id);
        $this->roles = new ArrayCollection();
        $this->gamesMastered = new ArrayCollection();
        $this->worldsCreated = new ArrayCollection();
        $this->worldsPlayed = new ArrayCollection();
        $this->postalAddresses = new ArrayCollection();
        $this->games = new ArrayCollection();
        $this->feats = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->userBlocks = $userBlocks ?? new ArrayCollection();
        $this->characters = new ArrayCollection();
        if ($this->userCart === null) {
            $this->userCart = new UserCart($this);
        }
        if ($this->userPreferences === null) {
            $this->userPreferences = new UserPreferences();
        }
    }

    /** @return Collection<int, Order> */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    /**
     * @return Collection<int, CampaignMembership>
     */
    public function getCampaignMemberships(): Collection
    {
        return $this->campaignMemberships;
    }

    public function addCampaignMembership(CampaignMembership $membership): void
    {
        if (!$this->campaignMemberships->contains($membership)) {
            $this->campaignMemberships->add($membership);
        }
    }

    public function removeCampaignMembership(CampaignMembership $membership): void
    {
        $this->campaignMemberships->removeElement($membership);
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function setOrganisation(?Organisation $organisation): void
    {
        $this->organisation = $organisation;
    }

    public function getAvatar(): ?ManagedImage
    {
        return $this->avatar;
    }

    public function setAvatar(ManagedImage $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function setProfilePublic(bool $profilePublic): void
    {
        $this->getUserPreferences()->setProfilePublic($profilePublic);
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
        return $this->getUserPreferences()->getHasAcceptedCookies();
    }

    public function setHasAcceptedCookies(bool $hasAcceptedCookies): void
    {
        $this->getUserPreferences()->setHasAcceptedCookies($hasAcceptedCookies);
    }

    public function isProfilePublic(): bool
    {
        return $this->getUserPreferences()->isProfilePublic();
    }

    public function setIsProfilePublic(bool $isProfilePublic): void
    {
        $this->getUserPreferences()->setProfilePublic($isProfilePublic);
    }


    /**
     * @return Collection<int, Game>
     */
    public function getGamesMastered(bool $includeArchived = false): Collection
    {
        $criteria = (new Criteria(accessRawFieldValues: true))
            ->where(Criteria::expr()->isNull('archivedDate'));

        return $includeArchived ? $this->gamesMastered : $this->gamesMastered->matching($criteria);
    }

    /**
     * @return Collection<int, Game>
     */
    public function getArchivedGamesMastered(): Collection
    {
        $criteria = (new Criteria(accessRawFieldValues: true))
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

    public function hasPermissionKey(string $permissionKey): bool {
        foreach ($this->roles as $role) {
            foreach ($role->getPermissions() as $permission) {
                if ($permission->getHandle() === $permissionKey) {
                    return true;
                }
            }
        }
        return false;
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

    /** @return Collection<int, UserBlock> */
    public function getUserBlock(): Collection {
        return $this->userBlocks;
    }

    public function getLatestUserBlock(): ?UserBlock
    {
        $block = $this->getUserBlock()->last();
        return $block instanceof UserBlock ? $block : null;
    }


    public function isBlocked(): bool
    {
        return !$this->getUserBlock()->isEmpty();
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

    /** @return Collection<int, PostalAddress> */
    public function getPostalAddresses(): Collection
    {
        return $this->postalAddresses;
    }

    /** @param Collection<int, PostalAddress> $postalAddresses */
    public function setPostalAddresses(Collection $postalAddresses): void
    {
        $this->postalAddresses = $postalAddresses;
    }

    public function getUserCart(): UserCart {
        if (null === $this->userCart) {
            $this->userCart = new UserCart($this);
        }
        return $this->userCart;
    }

    public function getUserPreferences(): UserPreferences {
        if (null === $this->userPreferences) {
            $this->userPreferences = new UserPreferences();
        }
        return $this->userPreferences;
    }

    /** @return Collection<int, World> */
    public function getWorldsCreated(): Collection
    {
        return $this->worldsCreated;
    }

    public function addWorld(World $world): void {
        if (!$this->worldsCreated->contains($world)) {
            $this->worldsCreated->add($world);
            $world->setCreator($this);
        }
    }

    /** @return Collection<int, Character> */
    public function getCharacters(): Collection
    {
        return $this->characters;
    }

    public function addCharacter(Character $character): void
    {
        if (!$this->characters->contains($character)) {
            $this->characters->add($character);
            $character->setUser(user: $this);
        }
    }

    public function removeCharacter(Character $character): void
    {
        $this->characters->removeElement($character);
    }
}
