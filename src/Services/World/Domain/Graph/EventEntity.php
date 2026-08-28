<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Graph;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'world_event')]
class EventEntity extends AbstractWorldGraphEntity
{
    /** @param Collection<int, WorldEntityRevision> $revisions */
    public function __construct(
        string $slug,
        string $currentName,

        #[ORM\Column(name: 'start_at', type: 'datetime_immutable', nullable: false)]
        private \DateTimeImmutable $startAt,

        #[ORM\Column(name: 'end_at', type: 'datetime_immutable', nullable: false)]
        private \DateTimeImmutable $endAt,

        #[ORM\ManyToOne(targetEntity: LocationEntity::class)]
        #[ORM\JoinColumn(name: 'location_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
        private ?LocationEntity $location = null,

        #[ORM\Column(type: 'text', nullable: true)]
        private ?string $summary = null,

        ?\DateTimeImmutable $revealedAt = null,
        Collection $revisions = new ArrayCollection(),
        ?WorldEntityRevision $currentRevision = null,
        ? int $id = null
    ) {
        parent::__construct(
            slug: $slug,
            currentName: $currentName,
            revealedAt: $revealedAt,
            revisions: $revisions,
            currentRevision: $currentRevision,
            id: $id
        );
    }



    public function getStartAt(): \DateTimeImmutable
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeImmutable $startAt): void
    {
        $this->startAt = $startAt;
    }

    public function getEndAt(): \DateTimeImmutable
    {
        return $this->endAt;
    }

    public function setEndAt(\DateTimeImmutable $endAt): void
    {
        $this->endAt = $endAt;
    }

    public function getLocation(): ?LocationEntity
    {
        return $this->location;
    }

    public function setLocation(?LocationEntity $location): void
    {
        $this->location = $location;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): void
    {
        $this->summary = $summary;
    }
}
