<?php

declare(strict_types=1);

namespace App\Services\User\Domain\UserCart;

use App\Services\Core\Domain\AbstractDomainEntity;

use App\Services\Product\Domain\ProductVariant;
use App\Services\User\Infrastructure\UserCart\UserCartLineRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity(repositoryClass: UserCartLineRepository::class)]
class UserCartLine extends AbstractDomainEntity implements JsonSerializable
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: UserCart::class, cascade: ['persist', 'refresh'], fetch: 'EAGER', inversedBy: 'userCartLines')]
        private readonly UserCart $userCart,

        #[ORM\ManyToOne(targetEntity: ProductVariant::class)]
        private readonly ProductVariant $productVariant,

        #[ORM\Column(type: 'integer')]
        private int $quantity,
        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getUserCart(): UserCart {
        return $this->userCart;
    }

    public function getProductVariant(): ProductVariant {
        return $this->productVariant;
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
            'name' => $this->getProductVariant()->getProductVariantName(),
            'productSku' => $this->getProductVariant()->getSku(),
            'preTaxPriceCentessimal' => $this->getPretaxTotalCentessimal(),
            'quantity' => $this->getQuantity(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
        ];
    }

    public function getPretaxTotalCentessimal(): float {
        return ($this->getProductVariant()->getPreTaxPriceCentessimal() * $this->getQuantity());
    }
    public function getPretaxTotalDecimal(): float
    {
        return $this->getPretaxTotalCentessimal()/100;
    }
}
