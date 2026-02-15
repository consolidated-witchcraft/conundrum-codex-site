<?php
declare(strict_types=1);

namespace App\Services\Ticket\Dto;

use App\Services\Ticket\Domain\TicketType;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

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
        public ? int $priceCentessimal = null,

        #[Assert\NotNull()]
        #[Assert\Type('integer')]
        public ? int $initialQuantity = null,

        public array $ticketTypeFormats = [],

        public ? \DateTimeInterface $refundableUntil = null,
        public ? int $id = null
    ) {}

    public static function makeFromTicketType(TicketType $ticketType): TicketTypeDto {

        return new self(
            name: $ticketType->getName(),
            slug: $ticketType->getSlug(),
            description: $ticketType->getDescription(),
            priceCentessimal: $ticketType->getPriceCentessimal(),
            initialQuantity: 123,
            ticketTypeFormats: self::mapTicketTypeFormats($ticketType->getTicketTypeFormats()),
            refundableUntil: $ticketType->getRefundableUntil(),
            id: $ticketType->getId()
        );
    }

    private static function mapTicketTypeFormats(Collection $ticketTypeFormats): array {
        $formats = [];
        foreach ($ticketTypeFormats as $ticketTypeFormat) {

        }
        return $formats;
    }

    public function isEmpty(): bool {
        return $this->name === null && $this->slug === null && $this->description === null && $this->id === 0;
    }
}
