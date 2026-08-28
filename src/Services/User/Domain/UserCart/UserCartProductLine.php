<?php

declare(strict_types=1);

namespace App\Services\User\Domain\UserCart;

use App\Services\Core\Domain\AbstractDomainEntity;

use App\Services\Product\Domain\ProductVariant;
use App\Services\User\Infrastructure\UserCart\UserCartProductLineRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity(repositoryClass: UserCartProductLineRepository::class)]
class UserCartProductLine extends AbstractDomainEntity implements JsonSerializable
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: UserCart::class, cascade: ['persist', 'refresh'], fetch: 'EAGER', inversedBy: 'userCartProductLines')]
        #[ORM\JoinColumn(nullable: false)]
        private readonly UserCart $userCart,

        #[ORM\ManyToOne(targetEntity: ProductVariant::class)]
        #[ORM\JoinColumn(nullable: false)]
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

    /** @return array<string, int|string|\DateTimeImmutable|null> */
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

    public function getPretaxTotalCentessimal(): int {
        return ($this->getProductVariant()->getPreTaxPriceCentessimal() * $this->getQuantity());
    }

    public function getPretaxTotalDecimal(): float
    {
        return $this->getPretaxTotalCentessimal()/100;
    }
}
