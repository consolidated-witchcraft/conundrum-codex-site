<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Game\Sessions;

use App\Services\Game\Domain\Game;
use App\Services\Game\Domain\GameSession;
use Symfony\Component\Validator\Constraints as Assert;
class GameSessionDto
{
    final public function __construct(
        public Game $game,

        #[Assert\NotBlank]
        #[Assert\Length(min: 5, max: 255)]
        public ? string $title = null,

        #[Assert\Length(min: 0, max: 1024)]
        public ? string $description = null,

        #[Assert\NotBlank]
        public ? \DateTimeInterface $sessionDate = null,

        public ? \DateTimeInterface $cancellationDate = null,
    ) {}

    public static function makeFromGameSession(GameSession $gameSession): static {
        return new static(
            game: $gameSession->getGame(),
            title: $gameSession->getTitle(),
            description: $gameSession->getDescription(),
            sessionDate: $gameSession->getSessionDate(),
            cancellationDate: $gameSession->getCancellationDate(),
        );
    }
}
