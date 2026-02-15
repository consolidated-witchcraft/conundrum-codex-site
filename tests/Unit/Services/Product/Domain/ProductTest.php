<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\Product\Domain;

use App\Services\Products\Domain\Product;
use App\Services\Products\Domain\ProductVariant;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testConstruct(): void {
        $baseSKU = 'ORGANISATION.EVENT.TICKETSLUG.BASE';
        $name = "Test Product";
        $description = "A test product";
        $productVariants = new ArrayCollection([
            $this->createStub(ProductVariant::class),
            $this->createStub(ProductVariant::class),
        ]);
        $id = 1;
        $product = new Product(
            baseSku: $baseSKU,
            name: $name,
            description: $description,
            productVariants: $productVariants,
            id: $id,
        );
        self::assertSame($baseSKU, $product->getBaseSKU());
        self::assertSame($name, $product->getName());
        self::assertSame($description, $product->getDescription());
        self::assertSame($productVariants, $product->getProductVariants());
        self::assertSame($id, $product->getId());
    }
}
