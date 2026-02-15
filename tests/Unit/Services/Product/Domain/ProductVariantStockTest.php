<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\Product\Domain;

use App\Services\Product\Domain\Exceptions\InsufficientStockException;
use App\Services\Product\Domain\ProductVariant;
use App\Services\Product\Domain\ProductVariantStock;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class ProductVariantStockTest extends TestCase
{
    public function testConstruct(): void {
        $productVariant = $this->createStub(ProductVariant::class);
        $stockLevel = 100;
        $productVariantStock = new ProductVariantStock(
            stockLevel: $stockLevel,
            productVariants: new ArrayCollection([$productVariant])
        );
        $this->assertEquals($stockLevel, $productVariantStock->getStockLevel());
    }

    public function testIncreaseStockLevel(): void {
        $productVariant = $this->createStub(ProductVariant::class);
        $productVariantStock = new ProductVariantStock(
            stockLevel: 100,
            productVariants: new ArrayCollection([$productVariant])
        );
        $productVariantStock->increaseStockLevel(5);
        $this->assertEquals(105, $productVariantStock->getStockLevel());
    }

    /**
     * @throws InsufficientStockException
     */
    public function testDecreaseStockLevel(
    ): void {
        $productVariant = $this->createStub(ProductVariant::class);
        $productVariantStock = new ProductVariantStock(
            stockLevel: 100,
            productVariants: new ArrayCollection([$productVariant])
        );
        $productVariantStock->decreaseStockLevel(5);
        $this->assertEquals(95, $productVariantStock->getStockLevel());
    }

    public function testInvalidDecreaseStockLevel(): void {
        $productVariant = $this->createConfiguredStub(ProductVariant::class, [
            'getSku' => "TEST.SKU"
        ]);
        $productVariantStock = new ProductVariantStock(
            stockLevel: 100,
            productVariants: new ArrayCollection([$productVariant])
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
            stockLevel: 100,
            productVariants: new ArrayCollection([$productVariant])
        );
        $productVariantStock->setStockLevel(10);
        $this->assertEquals(10, $productVariantStock->getStockLevel());
    }

    public function testSetProductVariants(): void {
        $productVariantStock = new ProductVariantStock(
            stockLevel: 100,
        );
        $productVariants = new ArrayCollection([$this->createStub(ProductVariant::class)]);
        self::assertEmpty($productVariantStock->getProductVariants());
        $productVariantStock->setProductVariants($productVariants);
        self::assertEquals($productVariants, $productVariantStock->getProductVariants());
    }

}
