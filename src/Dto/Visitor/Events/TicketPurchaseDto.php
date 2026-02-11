<?php


declare(strict_types=1);
namespace App\Dto\Visitor\Events;

use App\Services\Events\Domain\Event;
use App\Services\Events\Domain\TicketType;
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
        foreach ($ticketTypes as $ticketType) {
            $mappedTicketTypes[$ticketType->getSlug()] = [
                'name' => $ticketType->getName(),
                'priceCentessimal' => $ticketType->getPriceCentessimal(),
                'description' => $ticketType->getDescription(),
                'quantity' => 0
            ];
        }
        return $mappedTicketTypes;
    }
}
