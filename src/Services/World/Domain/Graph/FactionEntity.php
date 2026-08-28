<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Graph;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'world_faction')]
class FactionEntity extends AbstractWorldGraphEntity
{
    /** @param Collection<int, WorldEntityRevision> $revisions */
    public function __construct(
        string               $slug,
        string               $currentName,

        #[ORM\Column(type: 'string', length: 100)]
        private string       $factionName,

        ?\DateTimeImmutable  $revealedAt = null,
        Collection           $revisions = new ArrayCollection(),
        ?WorldEntityRevision $currentRevision = null,
        ?int                 $id = null

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

    public function getFactionName(): string
    {
        return $this->factionName;
    }

    public function setFactionName(string $factionName): void
    {
        $this->factionName = $factionName;
    }
}
