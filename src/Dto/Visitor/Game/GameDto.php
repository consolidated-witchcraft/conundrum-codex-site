<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Game;

use App\Services\Game\Domain\Game;
use Symfony\Component\Validator\Constraints as Assert;

class GameDto
{
    private const string SLUG_REGEX = '/^[a-z0-9_]+$/';

    final public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 5, max: 512)]
        public ? string $name = null,

        #[Assert\NotBlank]
        #[Assert\Length(min: 10, max: 1024)]
        public ? string $description = null,

        public ? string $heroImageUrl = null,

        #[Assert\NotBlank]
        #[Assert\Length(min: 6, max: 128)]
        #[Assert\Regex(self::SLUG_REGEX, message: 'The slug must only contain lowercase letters, numbers, and underscores.')]
        public ? string $slug = null
    ) {}

    public static function makeFromGame(Game $game): static {
        return new static(
            name: $game->getName(),
            description: $game->getDescription(),
            heroImageUrl: $game->getHeroImageFile()?->getPath(),
            slug: $game->getSlug(),
        );
    }
}
