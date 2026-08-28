<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Domain\Dynamic;

use App\Services\Core\Domain\AbstractDomainEntity;

class PuzzleInstanceRevealableClue extends AbstractDomainEntity
{
    public function __construct(
        private readonly string $title,
        private readonly string $body,
        private readonly string $slug,
        private readonly PuzzleInstance $puzzleInstance,
        private ? \DateTimeImmutable $revealedAt = null,
        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getBody(): string {
        return $this->body;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getPuzzleInstance(): PuzzleInstance
    {
        return $this->puzzleInstance;
    }

    public function getRevealedAt(): ? \DateTimeImmutable {
        return $this->revealedAt;
    }

    public function markRevealed(): void {
        $this->revealedAt = new \DateTimeImmutable();
    }
}
