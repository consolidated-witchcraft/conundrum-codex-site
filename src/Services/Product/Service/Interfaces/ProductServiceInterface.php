<?php

namespace App\Services\Product\Service\Interfaces;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\Product\Domain\Product;
use App\Services\Product\Domain\ProductVariant;

interface ProductServiceInterface extends DomainEntityServiceInterface
{
    public function getProductBySku(string $sku, bool $includeRetired = false): ?Product;

    public function saveProduct(Product $product, bool $flush = true): Product;

    public function removeProduct(Product $product, bool $flush = true): void;

    public function getProductVariantBySku(string $sku, bool $includeRetired = false): ?ProductVariant;

    public function saveProductVariant(ProductVariant $productVariant, bool $flush = true): ProductVariant;

    public function removeProductVariant(ProductVariant $productVariant, bool $flush = true): void;
}
