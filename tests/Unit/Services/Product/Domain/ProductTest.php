<?php

declare(strict_types=1);

use App\Services\Events\Domain\Organisation;
use App\Services\Product\Domain\Product;
use App\Services\Product\Domain\ProductVariant;
use Doctrine\Common\Collections\ArrayCollection;

test('construct', function () {
    $baseSKU = 'ORGANISATION.EVENT.TICKETSLUG.BASE';
    $name = "Test Product";
    $description = "A test product";
    $organisation = $this->createStub(Organisation::class);
    $productVariants = new ArrayCollection([
        $this->createStub(ProductVariant::class),
        $this->createStub(ProductVariant::class),
    ]);
    $id = 1;
    $product = new Product(
        baseSku: $baseSKU,
        name: $name,
        organisation: $organisation,
        description: $description,
        productVariants: $productVariants,
        id: $id,
    );
    self::assertSame($baseSKU, $product->getBaseSKU());
    self::assertSame($name, $product->getName());
    self::assertSame($organisation, $product->getOrganisation());
    self::assertSame($description, $product->getDescription());
    self::assertSame($productVariants, $product->getProductVariants());
    self::assertSame($id, $product->getId());
});
