<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Graph;

use App\Services\Core\Domain\AbstractDomainEntity;

use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'world_entity_revision')]
#[ORM\UniqueConstraint(name: 'uniq_entity_revision_number', columns: ['entity_id', 'revision_number'])]
#[ORM\Index(name: 'idx_world_entity_revision_entity', columns: ['entity_id'])]
class WorldEntityRevision extends AbstractDomainEntity
{
    /**
     * @param array<array-key, mixed>|null $astJson
     * @param array<array-key, mixed>|null $parseErrorsJson
     * @param Collection<int, WorldEdge> $projectedEdges
     * @param Collection<int, WorldFact> $projectedFacts
     * @param Collection<int, EventParticipation> $eventParticipations
     */
    public function __construct(
        #[ORM\ManyToOne(targetEntity: AbstractWorldGraphEntity::class, inversedBy: 'revisions')]
        #[ORM\JoinColumn(name: 'entity_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
        private AbstractWorldGraphEntity $entity,

        #[ORM\Column(name: 'revision_number', type: 'integer')]
        private int $revisionNumber,

        #[ORM\Column(name: 'source_markdown', type: 'text')]
        private string $sourceMarkdown,

        #[ORM\Column(name: 'source_hash', type: 'string', length: 64)]
        private string $sourceHash,

        #[ORM\Column(name: 'parser_version', type: 'string', length: 32)]
        private string $parserVersion,

        #[ORM\Column(name: 'ast_json', type: 'json', nullable: true)]
        private ?array $astJson = null,

        #[ORM\Column(name: 'ast_source_hash', type: 'string', length: 64, nullable: true)]
        private ?string $astSourceHash = null,


        #[ORM\Column(name: 'parse_errors_json', type: 'json', nullable: true)]
        private ?array $parseErrorsJson = null,

        #[ORM\Column(name: 'parse_status', type: 'string', length: 32)]
        private string $parseStatus = 'parsed',

        #[ORM\ManyToOne(targetEntity: User::class)]
        private ? User $createdBy = null,

        #[ORM\OneToMany(targetEntity: WorldEdge::class, mappedBy: 'sourceRevision', cascade: ['persist', 'remove'], orphanRemoval: true)]
        private Collection $projectedEdges = new ArrayCollection(),

        #[ORM\OneToMany(targetEntity: WorldFact::class, mappedBy: 'sourceRevision', cascade: ['persist', 'remove'], orphanRemoval: true)]
        private Collection $projectedFacts = new ArrayCollection(),

        #[ORM\OneToMany(mappedBy: 'sourceRevision', targetEntity: EventParticipation::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
        private Collection $eventParticipations = new ArrayCollection(),

        ? int $id = null
    ) {
        parent::__construct($id);
    }


    public function getEntity(): AbstractWorldGraphEntity
    {
        return $this->entity;
    }

    public function getRevisionNumber(): int
    {
        return $this->revisionNumber;
    }

    public function getSourceMarkdown(): string
    {
        return $this->sourceMarkdown;
    }

    public function getSourceHash(): string
    {
        return $this->sourceHash;
    }

    /** @param array<array-key, mixed>|null $astJson */
    public function setAstJson(?array $astJson): void
    {
        $this->astJson = $astJson;
    }

    /** @return array<array-key, mixed>|null */
    public function getAstJson(): ?array
    {
        return $this->astJson;
    }

    public function setAstSourceHash(?string $astSourceHash): void
    {
        $this->astSourceHash = $astSourceHash;
    }

    public function getAstSourceHash(): ?string
    {
        return $this->astSourceHash;
    }

    public function setParseStatus(string $parseStatus): void
    {
        $this->parseStatus = $parseStatus;
    }

    public function getParseStatus(): string
    {
        return $this->parseStatus;
    }

    /** @param array<array-key, mixed>|null $parseErrorsJson */
    public function setParseErrorsJson(?array $parseErrorsJson): void
    {
        $this->parseErrorsJson = $parseErrorsJson;
    }

    /** @return array<array-key, mixed>|null */
    public function getParseErrorsJson(): ?array
    {
        return $this->parseErrorsJson;
    }

    /**
     * @return Collection<int, WorldEdge>
     */
    public function getProjectedEdges(): Collection
    {
        return $this->projectedEdges;
    }

    /**
     * @return Collection<int, WorldFact>
     */
    public function getProjectedFacts(): Collection
    {
        return $this->projectedFacts;
    }

    /**
     * @return Collection<int, EventParticipation>
     */
    public function getEventParticipations(): Collection
    {
        return $this->eventParticipations;
    }

    public function getParserVersion(): string {
        return $this->parserVersion;
    }

    public function getCreatedBy(): ?User {
        return $this->createdBy;
    }
}
