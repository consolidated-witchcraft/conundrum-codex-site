<?php

declare(strict_types=1);

use App\Services\Ticket\Domain\Ticket;
use App\Services\Ticket\Domain\TicketType;
use App\Services\User\Domain\User;

test('construct', function () {
    $ticketType = $this->createStub(TicketType::class);
    $user = $this->createStub(User::class);
    $issuedDate = new DateTimeImmutable('2026-03-16 12:00:00');
    $paidDate = new DateTimeImmutable('2026-03-16 12:05:00');

    $ticket = new Ticket(
        customerEmail: 'customer@example.com',
        customerName: 'Customer Name',
        ticketCode: 'TICKET-123',
        ticketType: $ticketType,
        issuedDate: $issuedDate,
        paidDate: $paidDate,
        user: $user,
        id: 31,
    );

    self::assertSame('customer@example.com', $ticket->getCustomerEmail());
    self::assertSame('Customer Name', $ticket->getCustomerName());
    self::assertSame('TICKET-123', $ticket->getTicketCode());
    self::assertSame($ticketType, $ticket->getTicketType());
    self::assertSame($issuedDate, $ticket->getIssuedDate());
    self::assertSame($paidDate, $ticket->getPaidDate());
    self::assertSame($user, $ticket->getUser());
    self::assertSame(31, $ticket->getId());
});
