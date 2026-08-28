<?php

declare(strict_types=1);

namespace App\Services\Campaign\Domain;

use App\Services\Campaign\Domain\Enums\CampaignStatusEnum;
use App\Services\Campaign\Infrastructure\CampaignRepository;
use App\Services\Core\Domain\AbstractDomainEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CampaignRepository::class)]
#[UniqueEntity(fields: ['slug'], message: 'There is already a campaign with this slug. Please choose another one.')]
class Campaign extends AbstractDomainEntity
{
    /**
     * @param Collection<int, CampaignMembership> $memberships
     */
    public function __construct(
        #[ORM\Column(type: 'non_empty_string', length: 255)]
        #[Groups(['basic'])]
        private string $name,

        #[Groups(['basic'])]
        #[ORM\Column(type: 'non_empty_string', length: 255, unique: true)]
        private readonly string $slug,

        #[Groups(['extended'])]
        #[ORM\Column(type: 'non_empty_string', length: 1024)]
        private string $description,

        #[ORM\Column(enumType: CampaignStatusEnum::class)]
        private CampaignStatusEnum $status = CampaignStatusEnum::DRAFT,

        #[ORM\Column(type: 'datetime_immutable', nullable: true)]
        private ? \DateTimeImmutable $startDate = null,

        #[ORM\OneToMany(
            targetEntity: CampaignMembership::class,
            mappedBy: 'campaign',
            cascade: ['persist', 'remove'],
            orphanRemoval: true,
        )]
        private Collection $memberships = new ArrayCollection(),

        ?int $id = null
    ) {
        parent::__construct(
            id: $id
        );
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

    public function getSlug(): string {
        return $this->slug;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getStartDate(): \DateTimeImmutable {
        return $this->startDate;
    }

    public function markStarted(): void {
        $this->startDate = new \DateTimeImmutable();
        $this->status = CampaignStatusEnum::ACTIVE;
    }

    public function markDraft(): void {
        $this->status = CampaignStatusEnum::DRAFT;
    }

    public function markCompleted(): void {
        $this->status = CampaignStatusEnum::COMPLETED;
    }

    public function markArchived(): void {
        $this->status = CampaignStatusEnum::ARCHIVED;
    }

    public function getStatus(): CampaignStatusEnum {
        return $this->status;
    }

    /**
     * @return Collection<int, CampaignMembership>
     */
    public function getMemberships(): Collection
    {
        return $this->memberships;
    }

    public function addMembership(CampaignMembership $membership): void
    {
        if (!$this->memberships->contains($membership)) {
            $this->memberships->add($membership);
        }
    }

    public function removeMembership(CampaignMembership $membership): void
    {
        $this->memberships->removeElement($membership);
    }
}
