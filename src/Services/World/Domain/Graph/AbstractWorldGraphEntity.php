<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Graph;

use App\Services\Core\Domain\AbstractDomainEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'world_entity')]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorColumn(name: 'entity_type', type: 'string')]
#[ORM\DiscriminatorMap([
    'person' => PersonEntity::class,
    'location' => LocationEntity::class,
    'faction' => FactionEntity::class,
    'item' => ItemEntity::class,
    'event' => EventEntity::class,
])]
abstract class AbstractWorldGraphEntity extends AbstractDomainEntity
{
    /** @param Collection<int, WorldEntityRevision> $revisions */
    public function __construct(
        #[ORM\Column(type: 'string', length: 255, unique: true)]
        protected string $slug,

        #[ORM\Column(type: 'string', length: 255)]
        protected string $currentName,

        #[ORM\Column(type: 'datetime_immutable', nullable: true)]
        protected ?\DateTimeImmutable $revealedAt = null,

        #[ORM\OneToMany(targetEntity: WorldEntityRevision::class, mappedBy: 'entity', cascade: ['persist'])]
        protected Collection $revisions = new ArrayCollection(),

        #[ORM\ManyToOne(targetEntity: WorldEntityRevision::class)]
        #[ORM\JoinColumn(name: 'current_revision_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
        protected ?WorldEntityRevision $currentRevision = null,

        ?int $id = null
    ) {
        parent::__construct($id);
    }


    #[ORM\Column(type: 'string', length: 32, options: ['default' => 'canonical'])]
    protected string $status = 'canonical';

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getCurrentName(): string
    {
        return $this->currentName;
    }

    public function renameCurrent(string $currentName): void
    {
        $this->currentName = $currentName;}

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getRevealedAt(): ?\DateTimeImmutable
    {
        return $this->revealedAt;
    }

    public function setRevealedAt(?\DateTimeImmutable $revealedAt): void
    {
        $this->revealedAt = $revealedAt;
    }

    public function getCurrentRevision(): ?WorldEntityRevision
    {
        return $this->currentRevision;
    }

    public function setCurrentRevision(?WorldEntityRevision $currentRevision): void
    {
        $this->currentRevision = $currentRevision;
    }

    /**
     * @return Collection<int, WorldEntityRevision>
     */
    public function getRevisions(): Collection
    {
        return $this->revisions;
    }
}
