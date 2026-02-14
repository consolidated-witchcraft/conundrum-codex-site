<?php

declare(strict_types=1);

namespace App\Services\Ticket\Service\Interfaces;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\Events\Domain\Event;
use App\Services\Ticket\Domain\TicketType;
use App\Services\Ticket\Dto\TicketTypeDto;
use App\Services\User\Domain\User;

interface TicketServiceInterface extends DomainEntityServiceInterface
{
    /**
     * @param TicketTypeDto $ticketTypeDto
     * @param Event $event
     * @param User $creatingUser
     * @return TicketType
     */
    public function generateTicketTypeFromDto(TicketTypeDTO $ticketTypeDto, Event $event, User $creatingUser, bool $flush = true): TicketType;
}
