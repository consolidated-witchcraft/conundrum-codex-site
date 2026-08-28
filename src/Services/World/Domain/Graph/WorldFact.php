<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Graph;

use App\Services\Core\Domain\AbstractDomainEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'world_fact')]
#[ORM\Index(name: 'idx_world_fact_subject', columns: ['subject_entity_id'])]
#[ORM\Index(name: 'idx_world_fact_type', columns: ['fact_type'])]
#[ORM\Index(name: 'idx_world_fact_source_revision', columns: ['source_revision_id'])]
class WorldFact extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: AbstractWorldGraphEntity::class)]
        #[ORM\JoinColumn(name: 'subject_entity_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
        private readonly AbstractWorldGraphEntity $subjectEntity,

        #[ORM\Column(name: 'fact_type', type: 'string', length: 100)]
        private string $factType,

        #[ORM\Column(name: 'value_text', type: 'text')]
        private string $valueText,

        #[ORM\ManyToOne(targetEntity: WorldEntityRevision::class, inversedBy: 'projectedFacts')]
        #[ORM\JoinColumn(name: 'source_revision_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
        private readonly WorldEntityRevision $sourceRevision,

        #[ORM\Column(name: 'revealed_at', type: 'datetime_immutable', nullable: true)]
        private ?\DateTimeImmutable $revealedAt = null,

        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getSubjectEntity(): AbstractWorldGraphEntity
    {
        return $this->subjectEntity;
    }

    public function getSourceRevision(): WorldEntityRevision
    {
        return $this->sourceRevision;
    }

    public function getFactType(): string
    {
        return $this->factType;
    }

    public function setFactType(string $factType): void
    {
        $this->factType = $factType;
    }

    public function getValueText(): string
    {
        return $this->valueText;
    }

    public function setValueText(string $valueText): void
    {
        $this->valueText = $valueText;
    }

    public function getRevealedAt(): ?\DateTimeImmutable
    {
        return $this->revealedAt;
    }

    public function setRevealedAt(?\DateTimeImmutable $revealedAt): void
    {
        $this->revealedAt = $revealedAt;
    }
}
