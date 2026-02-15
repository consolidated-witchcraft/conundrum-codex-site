<?php

declare(strict_types=1);

namespace App\Services\Ticket\Domain\Exceptions;

class UnknownTicketFormatException extends AbstractTicketDomainException
{
    private const string MESSAGE_PATTERN = "Unknown ticket format '%s' specified";
    public function __construct(string $ticketFormatSlug, int $code = 0, ?\Throwable $previous = null) {
        $message = sprintf(
            self::MESSAGE_PATTERN,
            $ticketFormatSlug,
        );
        parent::__construct($message, $code, $previous);
    }
}
