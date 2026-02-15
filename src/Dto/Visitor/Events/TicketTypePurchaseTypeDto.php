<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Events;

use App\Services\Ticket\Domain\TicketType;

class TicketTypePurchaseTypeDto
{
    public function __construct(
        public readonly TicketType $ticketType,
        public int $quantity = 0,
        public ?string $selectedFormatSlug = null
    ) {

    }

    public static function makeFromTicketType(TicketType $ticketType): TicketTypePurchaseTypeDto {
        return new self($ticketType);
    }
}
