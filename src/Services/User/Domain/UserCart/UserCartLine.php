<?php

declare(strict_types=1);

namespace App\Services\User\Domain\UserCart;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Products\Domain\Product;
use App\Services\User\Infrastructure\UserCart\UserCartLineRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity(repositoryClass: UserCartLineRepository::class)]
class UserCartLine extends AbstractDomainEntity implements JsonSerializable
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: UserCart::class, cascade: ['persist', 'refresh'], fetch: 'EAGER', inversedBy: 'userCartLines')]
        private readonly UserCart $userCart,

        #[ORM\ManyToOne(targetEntity: Product::class)]
        private readonly Product $product,

        #[ORM\Column(type: 'integer')]
        private int $quantity,
        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getUserCart(): UserCart {
        return $this->userCart;
    }

    public function getProduct(): Product {
        return $this->product;
    }

    public function getQuantity(): int {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void {
        $this->quantity = $quantity;
    }

    public function jsonSerialize(): array
    {
        return [
            'cartLineId' => $this->getId(),
            'name' => $this->getProduct()->getName(),
            'productSku' => $this->getProduct()->getSku(),
            'preTaxPriceCentessimal' => $this->getPretaxTotalCentessimal(),
            'quantity' => $this->getQuantity(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
        ];
    }

    public function getPretaxTotalCentessimal(): float {
        return ($this->getProduct()->getPreTaxPriceCentessimal() * $this->getQuantity());
    }
    public function getPretaxTotalDecimal(): float
    {
        return $this->getPretaxTotalCentessimal()/100;
    }
}
