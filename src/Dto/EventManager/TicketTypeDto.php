<?php
declare(strict_types=1);

namespace App\Dto\EventManager;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as CustomAssert;
class TicketTypeDto
{
    private const string SLUG_REGEX = '/^[a-z0-9_]+$/';

    public function __construct(

        #[Assert\NotBlank()]
        #[Assert\Type('string')]
        public ? string $name = null,

        #[Assert\NotBlank()]
        #[Assert\Type('string')]
        #[Assert\Length(min: 4, max: 255)]
        #[Assert\Regex(self::SLUG_REGEX, message: 'The slug must only contain lowercase letters, numbers, and underscores.')]
        public ? string $slug = null,

        #[Assert\NotBlank()]
        #[Assert\Type('string')]
        #[Assert\Length(min: 4, max: 255)]
        public ? string $description = null,

        #[Assert\NotBlank()]
        #[Assert\Type('integer')]
        public ? int $priceCentessimal = null
    ) {}
}
