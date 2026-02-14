<?php

declare(strict_types=1);

namespace App\Services\Quotation\Service;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\Quotation\Domain\Quotation;
use App\Services\Quotation\Infrastructure\QuotationRepository;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class QuotationService
{
    private const string QUOTE_CACHE_KEY_PATTERN = "QUOTE_%d";
    private const int DEFAULT_CACHE_TTL = 31556952;
    public function __construct(
        private QuotationRepository $quotationRepository,
        private readonly CacheInterface $cache
    ) {}

    /**
     * @return array<int, Quotation>
     */
    public function findAll(): array
    {
        return $this->quotationRepository->findAll();
    }

    public function getRandomQuotation(): ? Quotation {
        $quoteId = $this->quotationRepository->getRandomQuotationId();
        return $this->cache->get(sprintf(self::QUOTE_CACHE_KEY_PATTERN, $quoteId), function (ItemInterface $item) use ($quoteId) {
            $item->expiresAfter(self::DEFAULT_CACHE_TTL);
            return $this->quotationRepository->find($quoteId);
        });
    }
}
