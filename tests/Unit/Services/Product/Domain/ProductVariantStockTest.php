<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\Product\Domain;

use App\Services\Products\Domain\Exceptions\InsufficientStockException;
use App\Services\Products\Domain\ProductVariant;
use App\Services\Products\Domain\ProductVariantStock;
use PHPUnit\Framework\TestCase;

class ProductVariantStockTest extends TestCase
{
    public function testConstruct(): void {
        $productVariant = $this->createStub(ProductVariant::class);
        $stockLevel = 100;
        $productVariantStock = new ProductVariantStock(
            productVariant: $productVariant,
            stockLevel: $stockLevel
        );
        $this->assertEquals($stockLevel, $productVariantStock->getStockLevel());
    }

    public function testIncreaseStockLevel(): void {
        $productVariant = $this->createStub(ProductVariant::class);
        $productVariantStock = new ProductVariantStock(
            productVariant: $productVariant,
            stockLevel: 100
        );
        $productVariantStock->increaseStockLevel(5);
        $this->assertEquals(105, $productVariantStock->getStockLevel());
    }

    public function testDecreaseStockLevel(
    ): void {
        $productVariant = $this->createStub(ProductVariant::class);
        $productVariantStock = new ProductVariantStock(
            productVariant: $productVariant,
            stockLevel: 100
        );
        $productVariantStock->decreaseStockLevel(5);
        $this->assertEquals(95, $productVariantStock->getStockLevel());
    }

    public function testInvalidDecreaseStockLevel(): void {
        $productVariant = $this->createConfiguredStub(ProductVariant::class, [
            'getSku' => "TEST.SKU"
        ]);
        $productVariantStock = new ProductVariantStock(
            productVariant: $productVariant,
            stockLevel: 100
        );
        self::expectException(InsufficientStockException::class);
        self::expectExceptionMessage(
            "Insufficient Stock for Product Variant TEST.SKU to satisfy request. 105 were requested, but only 100 remain."
        );
        $productVariantStock->decreaseStockLevel(105);
    }

    public function testSetStockLevel(): void {
        $productVariant = $this->createStub(ProductVariant::class);
        $productVariantStock = new ProductVariantStock(
            productVariant: $productVariant,
            stockLevel: 100
        );
        $productVariantStock->setStockLevel(10);
        $this->assertEquals(10, $productVariantStock->getStockLevel());
    }



}
