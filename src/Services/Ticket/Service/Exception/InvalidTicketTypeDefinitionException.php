<?php

namespace App\Services\Ticket\Service\Exception;

class InvalidTicketTypeDefinitionException extends AbstractTicketDomainException
{
    public function __construct(private array $errors, int $code = 0, ?\Throwable $previous = null) {
        parent::__construct(
            implode(', ', $errors),
            $code,
            $previous
        );
    }

    public function getErrors(): array {
        return $this->errors;
    }
}
