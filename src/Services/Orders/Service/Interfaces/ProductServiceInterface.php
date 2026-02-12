<?php

namespace App\Services\Orders\Service\Interfaces;

use App\Services\Core\Service\Interfaces\DomainServiceInterface;
use App\Services\Orders\Domain\Product;

interface ProductServiceInterface extends DomainServiceInterface
{
    public function getProductBySku(string $sku, bool $includeRetired = false): ?Product;

    public function saveProduct(Product $product): void;
}
