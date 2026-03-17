<?php

declare(strict_types=1);

use App\Services\Orders\Domain\Exceptions\UnknownProductTypeException;
use App\Services\Product\Domain\ValueObjects\ProductType;

dataset('product_types', function () {
    yield 'physical_ticket' => ['physical_ticket', 'Physical Ticket'];
    yield 'e_ticket' => ['e_ticket', 'Electronic Ticket'];
    yield 'merchandise' => ['merchandise', 'Merchandise'];
    yield 'subscription' => ['subscription', 'Subscription'];
    yield 'misc' => ['misc', 'Miscellaneous'];
});

test('make from handle', function (string $handle, string $name) {
    $productType = ProductType::makeFromHandle($handle);

    self::assertSame($handle, $productType->getHandle());
    self::assertSame($name, $productType->getName());
    self::assertSame($name, (string) $productType);
})->with('product_types');

test('unknown product type is rejected', function () {
    $this->expectException(UnknownProductTypeException::class);

    ProductType::makeFromHandle('bundle');
});
