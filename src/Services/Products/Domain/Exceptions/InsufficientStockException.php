<?php

declare(strict_types=1);

namespace App\Services\Products\Domain\Exceptions;

class InsufficientStockException extends AbstractProductDomainException
{

    private const string MESSAGE_PATTERN = "Insufficient Stock for Product Variant %s to satisfy request. %d were requested, but only %d remain.";
    public function __construct(string $productVariantSku, int $currentStockLevel, int $requestedStock, int $code = 0, \Throwable $previous = null) {
        $message = sprintf(self::MESSAGE_PATTERN, $productVariantSku, $requestedStock, $currentStockLevel);
        parent::__construct($message, $code, $previous);
    }
}
