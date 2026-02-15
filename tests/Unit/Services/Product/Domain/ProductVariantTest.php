<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\Product\Domain;

use App\Services\Files\Domain\ManagedFile;
use App\Services\Product\Domain\Product;
use App\Services\Product\Domain\ProductVariant;
use App\Services\Product\Domain\ProductVariantStock;
use App\Services\Product\Domain\ValueObjects\ProductType;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ProductVariantTest extends TestCase
{

    private function generateTestProductVariant(array $overrides = []): ProductVariant {
        return new ProductVariant(
            sku: $overrides['sku'] ?? 'ORGANISATION.EVENT.TICKETSLUG.ETICKET',
            productVariantName: $overrides['name'] ?? 'Test Product',
            type: $overrides['type'] ?? ProductType::makeFromHandle('e_ticket'),
            delta: $overrides['delta'] ?? 1,
            preTaxPriceCentessimal: $overrides['preTaxPriceCentessimal'] ?? 500,
            createdBy: $overrides['createdBy'] ?? $this->createStub(User::class),
            taxExempt: $overrides['taxExempt'] ?? false,
            requiresPostage: $overrides['requiresPostage'] ?? false,
            publicationDate: $overrides['publicationDate'] ?? new \DateTime(),
            productVariantStock: $overrides['productVariantStock'] ?? $this->createStub(ProductVariantStock::class),
            productVariantImages: $overrides['productVariantImages'] ?? new ArrayCollection([
                $this->createStub(ManagedFile::class),
            ]),
            retiredDate: $overrides['retiredDate'] ?? null,
            product: $overrides['product'] ?? $this->createStub(Product::class),
            id: $overrides['id'] ?? null,
        );
    }


    public function testConstruct(): void
    {

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
            $this->createStub(ManagedFile::class),
            $this->createStub(ManagedFile::class)
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
    }

    public function testSetDelta(): void {
        $productVariant = $this->generateTestProductVariant();
        $newDelta = 789;
        self::assertNotEquals($newDelta, $productVariant->getDelta());
        $productVariant->setDelta($newDelta);
        self::assertEquals($newDelta, $productVariant->getDelta());
    }

    public function testSetPreTaxPriceCentessimal(): void {
        $productVariant = $this->generateTestProductVariant();
        $newPrice = 2000;
        self::assertNotEquals($newPrice, $productVariant->getPreTaxPriceCentessimal());
        $productVariant->setPreTaxPriceCentessimal($newPrice);
        self::assertEquals($newPrice, $productVariant->getPreTaxPriceCentessimal());
    }

    public function testSetTaxExempt(): void {
        $productVariant = $this->generateTestProductVariant(
            [
                'taxExempt' => true,
            ]
        );
        self::assertTrue($productVariant->isTaxExempt());
        $productVariant->setTaxExempt(false);
        self::assertFalse($productVariant->isTaxExempt());
    }

    public function testSetRequiresPostage(): void {
        $productVariant = $this->generateTestProductVariant();
        self::assertFalse($productVariant->requiresPostage());
        $productVariant->setRequiresPostage(true);
        self::assertTrue($productVariant->requiresPostage());
    }

    public function testSetPublicationDate(): void {
        $productVariant = $this->generateTestProductVariant();
        $newPublicationDate = new \DateTime();
        $newPublicationDate->add(new \DateInterval('P3D'));
        self::assertNotEquals($newPublicationDate, $productVariant->getPublicationDate());
        $productVariant->setPublicationDate($newPublicationDate);
        self::assertEquals($newPublicationDate, $productVariant->getPublicationDate());
    }

    public function testSetProductVariantImages(): void {
        $productVariant = $this->generateTestProductVariant();
        $productVariantImages = new ArrayCollection([
            $this->createStub(ManagedFile::class),
            $this->createStub(ManagedFile::class),
            $this->createStub(ManagedFile::class),
            $this->createStub(ManagedFile::class),
        ]);
        self::assertNotEquals($productVariantImages, $productVariant->getProductVariantImages());
        $productVariant->setProductVariantImages($productVariantImages);
        self::assertEquals($productVariantImages, $productVariant->getProductVariantImages());
    }

    public function testAddProductVariantImage(): void {
        $productVariant = $this->generateTestProductVariant();
        $productVariantImages = new ArrayCollection([
            $this->createStub(ManagedFile::class),
            $this->createStub(ManagedFile::class),
            $this->createStub(ManagedFile::class),
            $this->createStub(ManagedFile::class),
        ]);
        $productVariant->setProductVariantImages($productVariantImages);

        $newImage = $this->createConfiguredStub(
            ManagedFile::class,
            [
                'getDescription' => 'Test Managed File Description'
            ]
        );
        $resultingCollection = clone $productVariantImages;
        $resultingCollection->add($newImage);

        $productVariant->addProductVariantImage($newImage);
        self::assertEquals($resultingCollection, $productVariant->getProductVariantImages());
    }

    public function testRemoveProductVariantImage(): void {
        $productVariant = $this->generateTestProductVariant();

        $newImage = $this->createConfiguredStub(
            ManagedFile::class,
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
    }

    public function testSetRetiredDate(): void {
        $productVariant = $this->generateTestProductVariant();
        self::assertNull($productVariant->getRetiredDate());
        $retiredDate = new \DateTime();
        $retiredDate->add(new \DateInterval('P1D'));
        $productVariant->setRetiredDate($retiredDate);
        self::assertEquals($retiredDate, $productVariant->getRetiredDate());
    }

    public function testSetProduct(): void {
        $productOne = $this->createConfiguredStub(Product::class, [
            'getName' => 'Test Product One'
        ]);
        $productTwo = $this->createConfiguredStub(Product::class, [
            'getName' => 'Test Product Two'
        ]);
        $productVariant = $this->generateTestProductVariant([
            'product' => $productOne,
        ]);
        self::assertEquals($productOne, $productVariant->getProduct());
        $productVariant->setProduct($productTwo);
        self::assertEquals($productTwo, $productVariant->getProduct());
    }


    public static function providePublicationDateData(): iterable {
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
    }

    #[DataProvider('providePublicationDateData')]
    public function testIsPublished(
        \DateTime $publicationDate,
        bool $expectedPublicationState
    ): void {
        $productVariant = $this->generateTestProductVariant([
            'publicationDate' => $publicationDate,
        ]);
        self::assertEquals($expectedPublicationState, $productVariant->isPublished());
    }

    public static function provideRetiredDateData(): iterable {
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
    }

    #[DataProvider('provideRetiredDateData')]
    public function testIsRetired(
        \DateTime $retiredDate,
        bool $expectedRetiredState
    ): void {
        $productVariant = $this->generateTestProductVariant([
            'retiredDate' => $retiredDate,
        ]);
        self::assertEquals($expectedRetiredState, $productVariant->isRetired());
    }


    public static function providePreTaxPriceDecimalData(): iterable {
        yield 'whole_number' => [
            'pricePreTaxCentessimal' => 1000,
            'expectedPricePreTaxDecimal' => 10.00,
        ];

        yield 'decimal' => [
            'pricePreTaxCentessimal' => 15,
            'expectedPricePreTaxDecimal' => 0.15,
        ];
    }

    #[DataProvider('providePreTaxPriceDecimalData')]
    public function testGetPreTaxPriceDecimal(
        int $pricePreTaxCentessimal,
        float $expectedPricePreTaxDecimal
    ): void {
        $productVariant = $this->generateTestProductVariant([
            'preTaxPriceCentessimal' => $pricePreTaxCentessimal,
        ]);
        self::assertEquals($expectedPricePreTaxDecimal, $productVariant->getPreTaxPriceDecimal());
    }
}
