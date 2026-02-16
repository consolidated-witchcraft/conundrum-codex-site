<?php

declare(strict_types=1);

namespace App\Services\Product\Service;

use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\Product\Domain\Product;
use App\Services\Product\Domain\ProductVariant;
use App\Services\Product\Infrastructure\ProductRepository;
use App\Services\Product\Infrastructure\ProductVariantRepository;
use App\Services\Product\Service\Interfaces\ProductServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

class ProductService extends AbstractDomainEntityService implements ProductServiceInterface
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly ProductVariantRepository $productVariantRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
    }

    public function getProductBySku(string $sku, bool $includeRetired = false): ?Product
    {
        return $this->productRepository->getProductBySku($sku, $includeRetired);
    }

    public function saveProduct(Product $product, bool $flush = true): Product
    {
        $this->entityManager->persist($product);
        if ($flush) {
            $this->entityManager->flush();
        }
        return $product;

    }

    public function removeProduct(Product $product, bool $flush = true): void
    {
        $this->entityManager->remove($product);
        if ($flush) {
            $this->flush();
        }
    }

    public function getProductVariantBySku(string $sku, bool $includeRetired = false): ?ProductVariant
    {
        return $this->productVariantRepository->findOneBy(['sku' => $sku]);
    }

    public function saveProductVariant(ProductVariant $productVariant, bool $flush = true): ProductVariant
    {
        $this->entityManager->persist($productVariant);
        if ($flush) {
            $this->flush();
        }
    }

    public function removeProductVariant(ProductVariant $productVariant, bool $flush = true): void
    {
        $this->entityManager->remove($productVariant);
        if ($flush) {
            $this->flush();
        }
    }
}
