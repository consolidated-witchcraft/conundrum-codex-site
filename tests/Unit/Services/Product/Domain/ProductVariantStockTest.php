<?php

declare(strict_types=1);
use App\Services\Product\Domain\Exceptions\InsufficientStockException;
use App\Services\Product\Domain\ProductVariant;
use App\Services\Product\Domain\ProductVariantStock;
use Doctrine\Common\Collections\ArrayCollection;
test('construct', function () {
    $productVariant = $this->createStub(ProductVariant::class);
    $stockLevel = 100;
    $productVariantStock = new ProductVariantStock(
        stockLevel: $stockLevel,
        productVariants: new ArrayCollection([$productVariant])
    );
    expect($productVariantStock->getStockLevel())->toEqual($stockLevel);
});
test('increase stock level', function () {
    $productVariant = $this->createStub(ProductVariant::class);
    $productVariantStock = new ProductVariantStock(
        stockLevel: 100,
        productVariants: new ArrayCollection([$productVariant])
    );
    $productVariantStock->increaseStockLevel(5);
    expect($productVariantStock->getStockLevel())->toEqual(105);
});
test('decrease stock level', function () {
    $productVariant = $this->createStub(ProductVariant::class);
    $productVariantStock = new ProductVariantStock(
        stockLevel: 100,
        productVariants: new ArrayCollection([$productVariant])
    );
    $productVariantStock->decreaseStockLevel(5);
    expect($productVariantStock->getStockLevel())->toEqual(95);
});
test('invalid decrease stock level', function () {
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
});
test('set stock level', function () {
    $productVariant = $this->createStub(ProductVariant::class);
    $productVariantStock = new ProductVariantStock(
        stockLevel: 100,
        productVariants: new ArrayCollection([$productVariant])
    );
    $productVariantStock->setStockLevel(10);
    expect($productVariantStock->getStockLevel())->toEqual(10);
});
test('set product variants', function () {
    $productVariantStock = new ProductVariantStock(
        stockLevel: 100,
    );
    $productVariants = new ArrayCollection([$this->createStub(ProductVariant::class)]);
    self::assertEmpty($productVariantStock->getProductVariants());
    $productVariantStock->setProductVariants($productVariants);
    self::assertEquals($productVariants, $productVariantStock->getProductVariants());
});
