<?php

declare(strict_types=1);

namespace App\Services\Orders\Service;

use App\Services\Orders\Domain\Product;
use App\Services\Orders\Infrastructure\ProductRepository;
use App\Services\Orders\Service\Interfaces\ProductServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class ProductService implements ProductServiceInterface
{
    public function __construct(
        private ProductRepository $productRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function getProductBySku(string $sku, bool $includeRetired = false): ?Product
    {
        return $this->productRepository->getProductBySku($sku, $includeRetired);
    }

    public function saveProduct(Product $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }
}
