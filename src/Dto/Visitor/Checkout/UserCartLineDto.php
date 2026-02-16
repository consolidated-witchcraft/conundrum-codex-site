<?php

declare(strict_types=1);

namespace App\Dto\Visitor\Checkout;

use App\Services\User\Domain\UserCart\UserCartProductLine;

class UserCartLineDto
{
    public function __construct(
        public string $productVariantSku,
        public string $productName,
        public int $productPricePreTaxCentessimal,
        public float $productPricePreTaxDecimal,
        public int $quantity,
        public string $productTypeHandle
    ) {
    }

    public function getFormSafeSku(): string {
        return str_replace('.', ':', $this->productVariantSku);
    }

    public static function makeFromUserCartLine(UserCartProductLine $userCartLine): static {
        return new static(
            productVariantSku: $userCartLine->getProductVariant()->getSku(),
            productName: $userCartLine->getProductVariant()->getProductVariantName(),
            productPricePreTaxCentessimal: $userCartLine->getProductVariant()->getPreTaxPriceCentessimal(),
            productPricePreTaxDecimal: $userCartLine->getProductVariant()->getPreTaxPriceDecimal(),
            quantity: $userCartLine->getQuantity(),
            productTypeHandle: $userCartLine->getProductVariant()->getType()->getHandle()

        );
    }
}
