<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Graph;

use App\Services\Core\Domain\AbstractDomainEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'world_event_participation')]
#[ORM\Index(name: 'idx_world_event_participation_event', columns: ['event_id'])]
#[ORM\Index(name: 'idx_world_event_participation_participant', columns: ['participant_entity_id'])]
#[ORM\Index(name: 'idx_world_event_participation_source_revision', columns: ['source_revision_id'])]
class EventParticipation extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: EventEntity::class)]
        #[ORM\JoinColumn(name: 'event_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
        private readonly EventEntity $event,

        #[ORM\ManyToOne(targetEntity: AbstractWorldGraphEntity::class)]
        #[ORM\JoinColumn(name: 'participant_entity_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
        private readonly AbstractWorldGraphEntity $participantEntity,

        #[ORM\Column(type: 'string', length: 100)]
        private readonly string $role,

        #[ORM\ManyToOne(targetEntity: WorldEntityRevision::class, inversedBy: 'eventParticipations')]
        #[ORM\JoinColumn(name: 'source_revision_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
        private readonly WorldEntityRevision $sourceRevision,

        #[ORM\Column(name: 'revealed_at', type: 'datetime_immutable', nullable: true)]
        private ?\DateTimeImmutable $revealedAt = null,

        #[ORM\Column(type: 'text', nullable: true)]
        private ?string $description = null,

        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getEvent(): EventEntity
    {
        return $this->event;
    }

    public function getParticipantEntity(): AbstractWorldGraphEntity
    {
        return $this->participantEntity;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function getSourceRevision(): WorldEntityRevision
    {
        return $this->sourceRevision;
    }

    public function getRevealedAt(): ?\DateTimeImmutable
    {
        return $this->revealedAt;
    }

    public function setRevealedAt(?\DateTimeImmutable $revealedAt): void
    {
        $this->revealedAt = $revealedAt;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

}
