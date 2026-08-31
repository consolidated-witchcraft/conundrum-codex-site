<?php

declare(strict_types=1);

namespace App\Dto\Visitor\User;

use App\Services\Character\Domain\Character;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class CharacterDto
{
    public const array VALID_PORTRAIT_EXTENSIONS = ['jpeg', 'jpg', 'png', 'webp'];
    public const array VALID_CHARACTER_SHEET_EXTENSIONS = ['jpeg', 'jpg', 'png', 'pdf'];

    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $name = '',

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $class = '',

        #[Assert\NotBlank]
        #[Assert\Length(max: 255)]
        public string $race = '',

        #[Assert\Positive]
        public int $level = 1,

        #[Assert\Image(
            maxSize: '5M',
            extensions: self::VALID_PORTRAIT_EXTENSIONS,
            extensionsMessage: 'Please upload a JPEG, PNG, or WebP image.',
        )]
        public ?UploadedFile $portrait = null,

        #[Assert\File(
            maxSize: '10M',
            extensions: self::VALID_CHARACTER_SHEET_EXTENSIONS,
            extensionsMessage: 'Please upload a JPEG, PNG, or PDF file.',
        )]
        public ?UploadedFile $characterSheet = null,

        public ?string $biography = null,
    ) {}

    public static function makeFromCharacter(Character $character): self
    {
        return new self(
            name: $character->getName(),
            class: $character->getClass(),
            race: $character->getRace(),
            level: $character->getLevel(),
            biography: $character->getBiography(),
        );
    }
}
