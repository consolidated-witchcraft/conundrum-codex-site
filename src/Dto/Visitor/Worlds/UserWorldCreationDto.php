<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Worlds;

use Symfony\Component\Validator\Constraints as Assert;
class UserWorldCreationDto
{
    private const string SLUG_REGEX = '/^[a-z0-9_]+$/';
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 10, max: 255)]
        public ? string $name = null,

        #[Assert\Length(min: 6, max: 255)]
        #[Assert\Regex(self::SLUG_REGEX, message: 'The slug must only contain lowercase letters, numbers, and underscores.')]
        public ? string $slug = null,

        #[Assert\Length(min: 1, max: 2198)]
        public ?string $description = null,
    ) {

    }
}
