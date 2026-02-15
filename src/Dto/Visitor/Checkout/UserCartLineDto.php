<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Checkout;

use App\Services\User\Domain\UserCart\UserCartLine;

class UserCartLineDto
{
    public function __construct(
        public string $productSku,
        public string $productName,
        public int $productPricePreTaxCentessimal,
        public float $productPricePreTaxDecimal,
        public int $quantity
    ) {
    }

    public function getFormSafeSku(): string {
        return str_replace('.', ':', $this->productSku);
    }

    public static function makeFromUserCartLine(UserCartLine $userCartLine): static {
        return new static(
            productSku: $userCartLine->getProduct()->getSku(),
            productName: $userCartLine->getProduct()->getName(),
            productPricePreTaxCentessimal: $userCartLine->getProduct()->getPreTaxPriceCentessimal(),
            productPricePreTaxDecimal: $userCartLine->getProduct()->getPreTaxPriceDecimal(),
            quantity: $userCartLine->getQuantity()
        );
    }
}
