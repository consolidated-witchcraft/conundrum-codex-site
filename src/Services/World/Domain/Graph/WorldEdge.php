<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Graph;

use App\Services\Core\Domain\AbstractDomainEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'world_edge')]
#[ORM\Index(name: 'idx_world_edge_from', columns: ['from_entity_id'])]
#[ORM\Index(name: 'idx_world_edge_to', columns: ['to_entity_id'])]
#[ORM\Index(name: 'idx_world_edge_type', columns: ['relationship_type'])]
#[ORM\Index(name: 'idx_world_edge_source_revision', columns: ['source_revision_id'])]
class WorldEdge extends AbstractDomainEntity
{

    public function __construct(
        #[ORM\ManyToOne(targetEntity: AbstractWorldGraphEntity::class)]
        #[ORM\JoinColumn(name: 'from_entity_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
        private readonly AbstractWorldGraphEntity $fromEntity,

        #[ORM\ManyToOne(targetEntity: AbstractWorldGraphEntity::class)]
        #[ORM\JoinColumn(name: 'to_entity_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
        private readonly AbstractWorldGraphEntity $toEntity,

        #[ORM\Column(name: 'relationship_type', type: 'string', length: 100)]
        private readonly string $relationshipType,

        #[ORM\ManyToOne(targetEntity: WorldEntityRevision::class, inversedBy: 'projectedEdges')]
        #[ORM\JoinColumn(name: 'source_revision_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
        private readonly WorldEntityRevision $sourceRevision,

        #[ORM\Column(name: 'revealed_at', type: 'datetime_immutable', nullable: true)]
        private ?\DateTimeImmutable $revealedAt = null,

        #[ORM\Column(name: 'valid_from', type: 'datetime_immutable', nullable: true)]
        private ?\DateTimeImmutable $validFrom = null,

        #[ORM\Column(name: 'valid_to', type: 'datetime_immutable', nullable: true)]
        private ?\DateTimeImmutable $validTo = null,

        #[ORM\Column(name: 'description', type: 'text', nullable: true)]
        private ?string $description = null,

        #[ORM\Column(name: 'is_inferred', type: 'boolean', options: ['default' => false])]
        private bool $isInferred = false,

        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function isInferred(): bool
    {
        return $this->isInferred;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getValidTo(): ?\DateTimeImmutable
    {
        return $this->validTo;
    }

    public function getValidFrom(): ?\DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function setRevealedAt(?\DateTimeImmutable $revealedAt): void
    {
        $this->revealedAt = $revealedAt;
    }

    public function setValidFrom(?\DateTimeImmutable $validFrom): void
    {
        $this->validFrom = $validFrom;
    }

    public function setValidTo(?\DateTimeImmutable $validTo): void
    {
        $this->validTo = $validTo;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setIsInferred(bool $isInferred): void
    {
        $this->isInferred = $isInferred;
    }

    public function getFromEntity(): AbstractWorldGraphEntity
    {
        return $this->fromEntity;
    }

    public function getToEntity(): AbstractWorldGraphEntity
    {
        return $this->toEntity;
    }

    public function getRelationshipType(): string
    {
        return $this->relationshipType;
    }

    public function getSourceRevision(): WorldEntityRevision
    {
        return $this->sourceRevision;
    }

    public function getRevealedAt(): ?\DateTimeImmutable
    {
        return $this->revealedAt;
    }
}
