<?php

declare(strict_types=1);

use App\Services\Ticket\Domain\Exceptions\UnknownTicketFormatException;
use App\Services\Ticket\Domain\ValueObjects\TicketFormat;

test('make from handle', function () {
    $ticketFormat = TicketFormat::makeFromHandle('e_ticket');

    self::assertSame('e_ticket', $ticketFormat->getHandle());
    self::assertSame('E-ticket', $ticketFormat->getName());
    self::assertStringContainsString('Q.R code', $ticketFormat->getDescription());
});

test('known formats for form are label to handle pairs', function () {
    $formatsForForm = TicketFormat::getKnownFormatsForForm();

    self::assertSame('e_ticket', $formatsForForm['E-ticket']);
    self::assertSame('physical_ticket', $formatsForForm['Physical Ticket']);
});

test('help text includes known ticket format descriptions', function () {
    $helpText = TicketFormat::generateHelpText();

    self::assertStringContainsString('<ul>', $helpText);
    self::assertStringContainsString('An E-Ticket is delivered via app and e-mail.', $helpText);
    self::assertStringContainsString('A Physical ticket is delivered via post', $helpText);
});

test('unknown handle is rejected', function () {
    $this->expectException(UnknownTicketFormatException::class);

    TicketFormat::makeFromHandle('sms_ticket');
});
