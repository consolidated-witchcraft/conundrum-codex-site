<?php

namespace App\Services\Orders\Service\Interfaces;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\Orders\Domain\Product;

interface ProductServiceInterface extends DomainEntityServiceInterface
{
    public function getProductBySku(string $sku, bool $includeRetired = false): ?Product;

    public function saveProduct(Product $product, bool $flush = true): Product;
}
