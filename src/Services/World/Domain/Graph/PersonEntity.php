<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Graph;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'world_person')]
class PersonEntity extends AbstractWorldGraphEntity
{
    /** @param Collection<int, WorldEntityRevision> $revisions */
    public function __construct(
        string $slug,
        string $currentName,
        ?\DateTimeImmutable $revealedAt = null,
        Collection $revisions = new ArrayCollection(),
        ?WorldEntityRevision $currentRevision = null,

        #[ORM\Column(type: 'string', length: 100, nullable: true)]
        private ?string $species = null,

        #[ORM\Column(type: 'string', length: 100, nullable: true)]
        private ?string $gender = null,
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

    public function getSpecies(): ?string
    {
        return $this->species;
    }

    public function setSpecies(?string $species): void
    {
        $this->species = $species;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): void
    {
        $this->gender = $gender;
    }
}
