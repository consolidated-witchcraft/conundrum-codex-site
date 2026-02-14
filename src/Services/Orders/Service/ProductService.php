<?php

declare(strict_types=1);

namespace App\Services\Orders\Service;

use App\Services\Orders\Domain\Product;
use App\Services\Orders\Infrastructure\ProductRepository;
use App\Services\Orders\Service\Interfaces\ProductServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\Core\Service\AbstractDomainEntityService;
class ProductService extends AbstractDomainEntityService implements ProductServiceInterface
{
    public function __construct(
        private readonly ProductRepository $productRepository,
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
}
