<?php

namespace App\Dto\EventManager;

use App\Services\Events\Domain\Event;
use App\Services\Events\Domain\Organisation;
use App\Services\Events\Domain\TicketType;
use Symfony\Component\Validator\Constraints as Assert;

class EventDto
{

    private const string SLUG_REGEX = '/^[a-z0-9_]+$/';
    public function __construct(
        public readonly Organisation $organisation,


        #[Assert\NotBlank()]
        #[Assert\Type('string')]
        #[Assert\Length(min: 4, max: 255)]
        public ? string $title = null,

        #[Assert\NotBlank()]
        #[Assert\Type('string')]
        #[Assert\Length(min: 4, max: 255)]
        #[Assert\Regex(self::SLUG_REGEX, message: 'The slug must only contain lowercase letters, numbers, and underscores.')]
        public ? string $eventSlug = null,

        #[Assert\NotBlank()]
        #[Assert\Type('string')]
        #[Assert\Length(min: 4, max: 2048)]
        public ? string $description = null,

        #[Assert\Type('boolean')]
        public bool $prebookOnly = false,


        #[Assert\Type('datetime')]
        public ?\DateTimeInterface $startDate = null,

        #[Assert\Type('datetime')]
        public ?\DateTimeInterface $endDate = null,
        /**
         * @var iterable<TicketType>|null
         */
        public ? iterable $ticketTypes = null,
    ) {}

    public static function makeFromEvent(Event $event): EventDto {
        return new static(
            organisation: $event->getOrganisation(),
            title: $event->getTitle(),
            eventSlug: $event->getEventSlug(),
            description: $event->getDescription(),
            prebookOnly: $event->isPrebookOnly(),
            startDate: $event->getStartDate(),
            endDate: $event->getEndDate(),
            ticketTypes: $event->getTicketTypes(),
        );
    }
}
