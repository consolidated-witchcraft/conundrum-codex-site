<?php

declare(strict_types=1);

use App\Services\Orders\Domain\ValueObjects\ChargeType;
use App\Services\Product\Domain\ProductVariant;
use App\Services\Product\Domain\ValueObjects\ProductType;
use App\Services\User\Domain\User;
use App\Services\User\Domain\UserCart\UserCart;
use App\Services\User\Domain\UserCart\UserCartCharge;
use App\Services\User\Domain\UserCart\UserCartProductLine;
use Doctrine\Common\Collections\ArrayCollection;

beforeEach(function () {
    $this->makeCart = function (array $overrides = []): UserCart {
        return new UserCart(
            user: $overrides['user'] ?? $this->createStub(User::class),
            userCartProductLines: $overrides['userCartProductLines'] ?? new ArrayCollection(),
            userCartCharges: $overrides['userCartCharges'] ?? new ArrayCollection(),
            id: $overrides['id'] ?? null,
        );
    };
});

test('construct', function () {
    $user = $this->createStub(User::class);
    $cart = ($this->makeCart)(['user' => $user, 'id' => 18]);

    self::assertSame($user, $cart->getUser());
    self::assertCount(0, $cart->getUserCartProductLines());
    self::assertCount(0, $cart->getUserCartChargeLines());
    self::assertSame(18, $cart->getId());
});

test('add product cart line merges existing sku and generates charges', function () {
    $cart = ($this->makeCart)();
    $ticketVariant = $this->createConfiguredStub(ProductVariant::class, [
        'getSku' => 'EVENT.TICKET',
        'requiresPostage' => false,
        'getType' => ProductType::makeFromHandle('e_ticket'),
        'getPreTaxPriceCentessimal' => 1000,
    ]);
    $physicalVariant = $this->createConfiguredStub(ProductVariant::class, [
        'getSku' => 'EVENT.PHYSICAL',
        'requiresPostage' => true,
        'getType' => ProductType::makeFromHandle('physical_ticket'),
        'getPreTaxPriceCentessimal' => 500,
    ]);

    $cart->addProductCartLine($ticketVariant, 1);
    $cart->addProductCartLine($ticketVariant, 2);
    $cart->addProductCartLine($physicalVariant, 1);

    self::assertCount(2, $cart->getUserCartProductLines());
    self::assertSame(3, $cart->getUserCartLineBySku('EVENT.TICKET')?->getQuantity());
    self::assertCount(2, $cart->getUserCartChargeLines());
    self::assertSame(3800, $cart->getPretaxTotalCentessimal());
    self::assertSame(38.0, $cart->getPretaxTotalDecimal());
});

test('cart line lookup and removal work by id', function () {
    $productVariant = $this->createConfiguredStub(ProductVariant::class, [
        'getSku' => 'EVENT.TICKET',
        'requiresPostage' => false,
        'getType' => ProductType::makeFromHandle('e_ticket'),
        'getPreTaxPriceCentessimal' => 1000,
    ]);
    $cart = ($this->makeCart)();
    $line = new UserCartProductLine(
        userCart: $cart,
        productVariant: $productVariant,
        quantity: 1,
        id: 55,
    );
    $charges = new ArrayCollection([
        new UserCartCharge($cart, ChargeType::makeFromHandle('service_charge'), 100),
    ]);
    $cart = ($this->makeCart)([
        'userCartProductLines' => new ArrayCollection([$line]),
        'userCartCharges' => $charges,
    ]);

    self::assertSame($line, $cart->getUserCartProductLineById(55));

    $cart->removeUserCartProductLine($line);
    self::assertNull($cart->getUserCartProductLineById(55));

    $cart->clearCart();
    self::assertCount(0, $cart->getUserCartProductLines());
});
