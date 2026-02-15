<?php

namespace App\Dto\Visitor\Events;

use App\Services\Ticket\Domain\TicketType;
use App\Services\Ticket\Domain\TicketTypeFormat;

readonly class TicketTypeFormatDto
{
    private function __construct(
        public string $ticketTypeFormatHandle,
        public string $ticketTypeFormatName,
        public string $ticketTypeFormatSku,
    ) {}
    public static function makeFromTicketFormat(TicketTypeFormat $ticketFormat): TicketTypeFormatDto {

        return new static(
            ticketTypeFormatHandle: $ticketFormat->getTicketFormat()->getHandle(),
            ticketTypeFormatName: $ticketFormat->getTicketFormat()->getName(),
            ticketTypeFormatSku: $ticketFormat->getProduct()->getSku(),
        );
    }
}
