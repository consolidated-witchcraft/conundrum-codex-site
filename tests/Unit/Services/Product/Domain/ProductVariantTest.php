<?php

declare(strict_types=1);
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\Product\Domain\Product;
use App\Services\Product\Domain\ProductVariant;
use App\Services\Product\Domain\ProductVariantStock;
use App\Services\Product\Domain\ValueObjects\ProductType;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;

beforeEach(function () {
    $this->makeProductVariant = function (array $overrides = []): ProductVariant {
        return new ProductVariant(
            sku: $overrides['sku'] ?? 'ORGANISATION.EVENT.TICKETSLUG.ETICKET',
            productVariantName: $overrides['name'] ?? 'Test Product',
            type: $overrides['type'] ?? ProductType::makeFromHandle('e_ticket'),
            delta: $overrides['delta'] ?? 1,
            preTaxPriceCentessimal: $overrides['preTaxPriceCentessimal'] ?? 500,
            createdBy: $overrides['createdBy'] ?? $this->createStub(User::class),
            publicationDate: $overrides['publicationDate'] ?? new \DateTime(),
            productVariantStock: $overrides['productVariantStock'] ?? $this->createStub(ProductVariantStock::class),
            taxExempt: $overrides['taxExempt'] ?? false,
            requiresPostage: $overrides['requiresPostage'] ?? false,
            productVariantImages: $overrides['productVariantImages'] ?? new ArrayCollection([
                $this->createStub(ManagedImage::class),
            ]),
            retiredDate: array_key_exists('retiredDate', $overrides) ? $overrides['retiredDate'] : new \DateTime('3000-01-01'),
            product: $overrides['product'] ?? $this->createStub(Product::class),
            id: $overrides['id'] ?? null,
        );
    };
});

test('construct', function () {
    $sku = 'ORGANISATION.EVENT.TICKETSLUG.ETICKET';
    $variantName = 'Test Product Variant Name';
    $type = ProductType::makeFromHandle('e_ticket');
    $delta = 1;
    $preTaxPriceCentessimal = 1000;
    $createdByUser = $this->createStub(User::class);
    $taxExempt = true;
    $publicationDate = new \DateTime();
    $retiredDate = clone $publicationDate;
    $retiredDate->add(new \DateInterval('P1D'));
    $productVariantImages = new ArrayCollection([
        $this->createStub(ManagedImage::class),
        $this->createStub(ManagedImage::class)
    ]);
    $productVariantStock = $this->createStub(ProductVariantStock::class);

    $product = $this->createStub(Product::class);
    $id = 456;
    $productVariant = new ProductVariant(
        sku: $sku,
        productVariantName: $variantName,
        type: $type,
        delta: $delta,
        preTaxPriceCentessimal: $preTaxPriceCentessimal,
        createdBy: $createdByUser,
        taxExempt: $taxExempt,
        requiresPostage: false,
        publicationDate: $publicationDate,
        productVariantStock: $productVariantStock,
        productVariantImages: $productVariantImages,
        retiredDate: $retiredDate,
        product: $product,
        id: $id
    );

    self::assertEquals($sku, $productVariant->getSku());
    self::assertEquals($variantName, $productVariant->getProductVariantName());
    self::assertSame($type, $productVariant->getType());
    self::assertEquals($delta, $productVariant->getDelta());
    self::assertEquals($preTaxPriceCentessimal, $productVariant->getPreTaxPriceCentessimal());
    self::assertEquals($createdByUser, $productVariant->getCreatedBy());
    self::assertSame($taxExempt, $productVariant->isTaxExempt());
    self::assertEquals($publicationDate, $productVariant->getPublicationDate());
    self::assertEquals($retiredDate, $productVariant->getRetiredDate());
    self::assertEquals($productVariantImages, $productVariant->getProductVariantImages());
    self::assertEquals($product, $productVariant->getProduct());
    self::assertEquals($id, $productVariant->getId());
    self::assertEquals($productVariantStock, $productVariant->getProductVariantStock());
});

test('set delta', function () {
    $productVariant = ($this->makeProductVariant)();
    $newDelta = 789;
    self::assertNotEquals($newDelta, $productVariant->getDelta());
    $productVariant->setDelta($newDelta);
    self::assertEquals($newDelta, $productVariant->getDelta());
});

test('set pre tax price centessimal', function () {
    $productVariant = ($this->makeProductVariant)();
    $newPrice = 2000;
    self::assertNotEquals($newPrice, $productVariant->getPreTaxPriceCentessimal());
    $productVariant->setPreTaxPriceCentessimal($newPrice);
    self::assertEquals($newPrice, $productVariant->getPreTaxPriceCentessimal());
});

test('set tax exempt', function () {
    $productVariant = ($this->makeProductVariant)([
        'taxExempt' => true,
    ]);
    self::assertTrue($productVariant->isTaxExempt());
    $productVariant->setTaxExempt(false);
    self::assertFalse($productVariant->isTaxExempt());
});

test('set requires postage', function () {
    $productVariant = ($this->makeProductVariant)();
    self::assertFalse($productVariant->requiresPostage());
    $productVariant->setRequiresPostage(true);
    self::assertTrue($productVariant->requiresPostage());
});

test('set publication date', function () {
    $productVariant = ($this->makeProductVariant)();
    $newPublicationDate = new \DateTime();
    $newPublicationDate->add(new \DateInterval('P3D'));
    self::assertNotEquals($newPublicationDate, $productVariant->getPublicationDate());
    $productVariant->setPublicationDate($newPublicationDate);
    self::assertEquals($newPublicationDate, $productVariant->getPublicationDate());
});

test('set product variant images', function () {
    $productVariant = ($this->makeProductVariant)();
    $productVariantImages = new ArrayCollection([
        $this->createStub(ManagedImage::class),
        $this->createStub(ManagedImage::class),
        $this->createStub(ManagedImage::class),
        $this->createStub(ManagedImage::class),
    ]);
    self::assertNotEquals($productVariantImages, $productVariant->getProductVariantImages());
    $productVariant->setProductVariantImages($productVariantImages);
    self::assertEquals($productVariantImages, $productVariant->getProductVariantImages());
});

test('add product variant image', function () {
    $productVariant = ($this->makeProductVariant)();
    $productVariantImages = new ArrayCollection([
        $this->createStub(ManagedImage::class),
        $this->createStub(ManagedImage::class),
        $this->createStub(ManagedImage::class),
        $this->createStub(ManagedImage::class),
    ]);
    $productVariant->setProductVariantImages($productVariantImages);

    $newImage = $this->createConfiguredStub(
        ManagedImage::class,
        [
            'getDescription' => 'Test Managed File Description'
        ]
    );
    $resultingCollection = clone $productVariantImages;
    $resultingCollection->add($newImage);

    $productVariant->addProductVariantImage($newImage);
    self::assertEquals($resultingCollection, $productVariant->getProductVariantImages());
});

test('remove product variant image', function () {
    $productVariant = ($this->makeProductVariant)();

    $newImage = $this->createConfiguredStub(
        ManagedImage::class,
        [
            'getDescription' => 'Test Managed File Description'
        ]
    );
    $productVariantImages = new ArrayCollection([
        1 => $newImage,
    ]);
    $productVariant->setProductVariantImages($productVariantImages);

    $resultingCollection = clone $productVariantImages;
    $resultingCollection->remove(1);

    $productVariant->removeProductVariantImage($newImage);
    self::assertEquals($resultingCollection, $productVariant->getProductVariantImages());
});

test('set retired date', function () {
    $productVariant = ($this->makeProductVariant)([
        'retiredDate' => null,
    ]);
    self::assertNull($productVariant->getRetiredDate());
    $retiredDate = new \DateTime();
    $retiredDate->add(new \DateInterval('P1D'));
    $productVariant->setRetiredDate($retiredDate);
    self::assertEquals($retiredDate, $productVariant->getRetiredDate());
});

test('set product', function () {
    $productOne = $this->createConfiguredStub(Product::class, [
        'getName' => 'Test Product One'
    ]);
    $productTwo = $this->createConfiguredStub(Product::class, [
        'getName' => 'Test Product Two'
    ]);
    $productVariant = ($this->makeProductVariant)([
        'product' => $productOne,
    ]);
    self::assertEquals($productOne, $productVariant->getProduct());
    $productVariant->setProduct($productTwo);
    self::assertEquals($productTwo, $productVariant->getProduct());
});
dataset('providePublicationDateData', function () {
    yield 'publication_date_in_past' => [
        'publicationDate' => new \DateTime('2000-01-01'),
        'expectedPublicationState' => true
    ];

    yield 'publication_date_today' => [
        'publicationDate' => new \DateTime(),
        'expectedPublicationState' => true
    ];

    yield 'publication_date_in_future' => [
        'publicationDate' => new \DateTime('3000-01-01'),
        'expectedPublicationState' => false
    ];
});
test('is published', function (\DateTime $publicationDate, bool $expectedPublicationState) {
    $productVariant = ($this->makeProductVariant)([
        'publicationDate' => $publicationDate,
    ]);
    self::assertEquals($expectedPublicationState, $productVariant->isPublished());
})->with('providePublicationDateData');
dataset('provideRetiredDateData', function () {
    yield 'retired_date_in_past' => [
        'retiredDate' => new \DateTime('2000-01-01'),
        'expectedRetiredState' => true
    ];

    yield 'retired_date_today' => [
        'retiredDate' => new \DateTime(),
        'expectedRetiredState' => true
    ];

    yield 'retired_date_in_future' => [
        'retiredDate' => new \DateTime('3000-01-01'),
        'expectedRetiredState' => false
    ];
});
test('is retired', function (\DateTime $retiredDate, bool $expectedRetiredState) {
    $productVariant = ($this->makeProductVariant)([
        'retiredDate' => $retiredDate,
    ]);
    self::assertEquals($expectedRetiredState, $productVariant->isRetired());
})->with('provideRetiredDateData');
dataset('providePreTaxPriceDecimalData', function () {
    yield 'whole_number' => [
        'pricePreTaxCentessimal' => 1000,
        'expectedPricePreTaxDecimal' => 10.00,
    ];

    yield 'decimal' => [
        'pricePreTaxCentessimal' => 15,
        'expectedPricePreTaxDecimal' => 0.15,
    ];
});
test('get pre tax price decimal', function (int $pricePreTaxCentessimal, float $expectedPricePreTaxDecimal) {
    $productVariant = ($this->makeProductVariant)([
        'preTaxPriceCentessimal' => $pricePreTaxCentessimal,
    ]);
    self::assertEquals($expectedPricePreTaxDecimal, $productVariant->getPreTaxPriceDecimal());
})->with('providePreTaxPriceDecimalData');
