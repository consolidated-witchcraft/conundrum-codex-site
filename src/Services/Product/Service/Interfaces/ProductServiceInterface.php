<?php

namespace App\Services\Product\Service\Interfaces;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\Product\Domain\Product;

interface ProductServiceInterface extends DomainEntityServiceInterface
{
    public function getProductBySku(string $sku, bool $includeRetired = false): ?Product;

    public function saveProduct(Product $product, bool $flush = true): Product;
}
