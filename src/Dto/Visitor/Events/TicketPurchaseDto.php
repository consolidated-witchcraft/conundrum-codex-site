<?php


declare(strict_types=1);
namespace App\Dto\Visitor\Events;

use App\Form\Type\TicketTypePurchaseType;
use App\Services\Events\Domain\Event;
use App\Services\Ticket\Domain\TicketType;
use App\Services\Ticket\Domain\TicketTypeProductVariantMap;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\Collection;

class TicketPurchaseDto
{
    private function __construct(
        public Event $event,
        public array $ticketTypes = [],
    ) {}

    public static function makeFromEvent(Event $event, User $user = null): static {
        return new static(
            event: $event,
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
            $mappedTicketTypes[$ticketType->getSlug()] = TicketTypePurchaseTypeDto::makeFromTicketType($ticketType);

        }
        return $mappedTicketTypes;
    }

}
