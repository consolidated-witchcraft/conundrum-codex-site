<?php

declare(strict_types=1);

namespace App\Services\World\Domain\Graph;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'world_item')]
class ItemEntity extends AbstractWorldGraphEntity
{
    public function __construct(
        string $slug,
        string $currentName,
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


}
