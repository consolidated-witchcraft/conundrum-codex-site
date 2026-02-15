<?php


declare(strict_types=1);
namespace App\Dto\Visitor\Events;

use App\Services\Events\Domain\Event;
use App\Services\Ticket\Domain\TicketType;
use App\Services\Ticket\Domain\TicketTypeFormat;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\Collection;

class TicketPurchaseDto
{
    private function __construct(
        public Event $event,
        public ? string $customerEmail= null,
        public ? string $customerName = null,
        public array $ticketTypes = [],
    ) {}

    public static function makeFromEvent(Event $event, ?User $user = null): static {
        return new static(
            event: $event,
            customerEmail: $user?->getEmail(),
            customerName: $user?->getUsername(),
            ticketTypes: self::mapTicketTypes($event->getTicketTypes()),
        );
    }

    /**
     * @param Collection<int, TicketType> $ticketTypes
     * @return array
     */
    private static function mapTicketTypes(Collection $ticketTypes): array {
        $mappedTicketTypes = [];
        /**
         * @var TicketType $ticketType
         */
        foreach ($ticketTypes as $ticketType) {

            $mappedTicketTypes[$ticketType->getSlug()] = VisitorTicketTypeDto::makeFromTicketType($ticketType);
        }
        return $mappedTicketTypes;
    }

    private static function mapFormats(Collection $ticketTypeFormats): array {
        $formats = [];

        /**
         * @var TicketTypeFormat $ticketTypeFormat
         */
        foreach ($ticketTypeFormats as $ticketTypeFormat) {
            $formats[$ticketTypeFormat->getTicketFormat()->getHandle()] = TicketTypeFormatDto::makeFromTicketFormat($ticketTypeFormat);
        }
        return $formats;
    }
}
