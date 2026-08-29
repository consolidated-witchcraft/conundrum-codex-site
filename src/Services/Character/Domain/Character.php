<?php

declare(strict_types=1);

namespace App\Services\Character\Domain;

use App\Services\Campaign\Domain\CampaignCharacter;
use App\Services\Character\Infrastructure\CharacterRepository;
use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CharacterRepository::class)]
class Character extends AbstractDomainEntity
{
    /**
     * @param Collection<int, CharacterNote> $characterNotes
     * @param Collection<int, CampaignCharacter> $campaignCharacters
     */
    public function __construct(
        #[ORM\Column(type: 'non_empty_string', length: 255)]
        private string $name,

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'characters')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private User $user,

        #[ORM\Column(name: 'character_class', type: 'non_empty_string', length: 255)]
        private string $class,

        #[ORM\Column(type: 'non_empty_string', length: 255)]
        private string $race,

        #[ORM\OneToOne(targetEntity: ManagedImage::class)]
        #[ORM\JoinColumn(nullable: false)]
        private ManagedImage $portrait,

        #[ORM\Column(options: ['default' => false])]
        private bool $npc = false,

        #[ORM\OneToOne(targetEntity: ManagedFile::class)]
        #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
        private ?ManagedFile $characterSheet = null,

        #[ORM\Column(type: Types::TEXT, nullable: true)]
        private ?string $biography = null,

        #[ORM\OneToMany(
            targetEntity: CharacterNote::class,
            mappedBy: 'character',
            cascade: ['persist', 'remove'],
            orphanRemoval: true,
        )]
        private Collection $characterNotes = new ArrayCollection(),

        #[ORM\OneToMany(
            targetEntity: CampaignCharacter::class,
            mappedBy: 'character',
        )]
        private Collection $campaignCharacters = new ArrayCollection(),

        ?int $id = null,
    ) {
        parent::__construct(id: $id);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function setClass(string $class): void
    {
        $this->class = $class;
    }

    public function getRace(): string
    {
        return $this->race;
    }

    public function setRace(string $race): void
    {
        $this->race = $race;
    }

    public function getPortrait(): ManagedImage
    {
        return $this->portrait;
    }

    public function setPortrait(ManagedImage $portrait): void
    {
        $this->portrait = $portrait;
    }

    public function isNpc(): bool
    {
        return $this->npc;
    }

    public function markAsNpc(): void
    {
        $this->npc = true;
    }

    public function unmarkAsNpc(): void
    {
        $this->npc = false;
    }

    public function getCharacterSheet(): ?ManagedFile
    {
        return $this->characterSheet;
    }

    public function setCharacterSheet(?ManagedFile $characterSheet): void
    {
        $this->characterSheet = $characterSheet;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): void
    {
        $this->biography = $biography;
    }

    /**
     * @return Collection<int, CharacterNote>
     */
    public function getCharacterNotes(): Collection
    {
        return $this->characterNotes;
    }

    public function addCharacterNote(CharacterNote $characterNote): void
    {
        if (!$this->characterNotes->contains($characterNote)) {
            $this->characterNotes->add($characterNote);
            $characterNote->setCharacter(character: $this);
        }
    }

    public function removeCharacterNote(CharacterNote $characterNote): void
    {
        $this->characterNotes->removeElement($characterNote);
    }

    /**
     * @return Collection<int, CampaignCharacter>
     */
    public function getCampaignCharacters(): Collection
    {
        return $this->campaignCharacters;
    }

    public function addCampaignCharacter(CampaignCharacter $campaignCharacter): void
    {
        if (!$this->campaignCharacters->contains($campaignCharacter)) {
            $this->campaignCharacters->add($campaignCharacter);
        }
    }

    public function removeCampaignCharacter(CampaignCharacter $campaignCharacter): void
    {
        $this->campaignCharacters->removeElement($campaignCharacter);
    }
}
