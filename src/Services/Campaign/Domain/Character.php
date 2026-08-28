<?php

declare(strict_types=1);

namespace App\Services\Campaign\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\ManagedFiles\Domain\ManagedFile;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Character extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\OneToOne(targetEntity: CampaignMembership::class, inversedBy: 'character')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private CampaignMembership $campaignMembership,

        #[ORM\Column(type: 'non_empty_string', length: 255)]
        private string $name,

        #[ORM\Column(type: 'text')]
        private string $description,

        #[ORM\OneToOne(targetEntity: ManagedFile::class)]
        #[ORM\JoinColumn(nullable: false)]
        private ManagedFile $avatar,

        #[ORM\OneToOne(targetEntity: ManagedFile::class)]
        #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
        private ?ManagedFile $characterSheet = null,

        ?int $id = null,
    ) {
        parent::__construct(id: $id);
    }

    public function getCampaignMembership(): CampaignMembership
    {
        return $this->campaignMembership;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getAvatar(): ManagedFile
    {
        return $this->avatar;
    }

    public function setAvatar(ManagedFile $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getCharacterSheet(): ?ManagedFile
    {
        return $this->characterSheet;
    }

    public function setCharacterSheet(?ManagedFile $characterSheet): void
    {
        $this->characterSheet = $characterSheet;
    }
}
