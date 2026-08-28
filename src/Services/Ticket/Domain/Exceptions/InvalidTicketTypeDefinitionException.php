<?php

declare(strict_types=1);

namespace App\Services\Ticket\Domain\Exceptions;

class InvalidTicketTypeDefinitionException extends AbstractTicketDomainException
{
    /** @param array<string, string> $errors */
    public function __construct(private array $errors, int $code = 0, ?\Throwable $previous = null) {
        parent::__construct(
            implode(', ', $errors),
            $code,
            $previous
        );
    }

    /** @return array<string, string> */
    public function getErrors(): array {
        return $this->errors;
    }
}
