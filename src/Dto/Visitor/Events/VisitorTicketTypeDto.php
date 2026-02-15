<?php

namespace App\Dto\Visitor\Events;

use App\Services\Ticket\Domain\TicketType;
use App\Services\Ticket\Domain\TicketTypeFormat;
use Doctrine\Common\Collections\Collection;

class VisitorTicketTypeDto
{
    private function __construct(
        public string $slug,
        public string $name,
        public string $description,
        public int $priceCentessimal,
        public array $ticketTypeFormats,
        public ? \DateTimeInterface $refundableUntil = null
    ) {}

    public static function makeFromTicketType(TicketType $ticketType): static {
        return new static(
            slug: $ticketType->getSlug(),
            name: $ticketType->getName(),
            description: $ticketType->getDescription(),
            priceCentessimal: $ticketType->getPriceCentessimal(),
            ticketTypeFormats: self::mapTicketTypeFormats($ticketType->getTicketTypeFormats()),
            refundableUntil: $ticketType->getRefundableUntil()
        );
    }

    /**
     * @param Collection<int, TicketTypeFormat> $ticketTypeFormats
     * @return array
     */
    private static function mapTicketTypeFormats(Collection $ticketTypeFormats): array {
        $formats = [];
        foreach ($ticketTypeFormats as $ticketTypeFormat) {
            $formats[$ticketTypeFormat->getTicketFormat()->getHandle()] = TicketTypeFormatDto::makeFromTicketFormat($ticketTypeFormat);
        }
        return $formats;
    }
}
