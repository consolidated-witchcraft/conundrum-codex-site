<?php

namespace App\Dto\EventManager;

use App\Services\Events\Domain\Event;

class TicketTypesDto
{
    /**
     * @param TicketTypeDto[] $ticketTypes
     */
    public function __construct(public iterable $ticketTypes) {}

    public static function makeFromEvent(Event $event): TicketTypesDto {
        $ticketTypes = [];
        foreach ($event->getTicketTypes() as $delta => $ticketType) {
            $ticketTypes[$delta] = TicketTypeDto::makeFromTicketType($ticketType);
        }
        return new self($ticketTypes);
    }
}
