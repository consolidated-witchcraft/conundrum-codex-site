<?php

declare(strict_types=1);

namespace App\ApiResource\Quote\Providers;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Quote\Dto\QuoteDto;
use App\Services\Quotation\Service\QuotationService;

class QuoteProvider implements ProviderInterface
{
    public function __construct(private QuotationService $quotationService) {

    }
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $quotation = $this->quotationService->getRandomQuotation();
        return QuoteDto::makeFromQuoteEntity($quotation);
    }
}
