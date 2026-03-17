<?php

declare(strict_types=1);

use App\Services\Core\Domain\Exceptions\UnknownValueObjectKeyException;
use App\Services\User\Domain\ValueObjects\PreferredFontStack;

dataset('preferred_font_stacks', function () {
    yield 'regular' => ['regular', 'Regular', 'regular'];
    yield 'sans-serif' => ['sans-serif', 'Sans Serif', 'sans serif'];
    yield 'dyslexic' => ['dyslexic', 'Dyslexic', 'dyslexic'];
});

test('make from key', function (string $key, string $label, string $classSafeLabel) {
    $fontStack = PreferredFontStack::makeFromKey($key);

    self::assertSame($key, $fontStack->getKey());
    self::assertSame($label, $fontStack->getLabel());
    self::assertSame($classSafeLabel, $fontStack->getClassSafeLabel());
    self::assertSame($key, (string) $fontStack);
})->with('preferred_font_stacks');

test('available font stacks are keyed by handle', function () {
    $availableFontStacks = PreferredFontStack::getAvailableFontStacks();

    self::assertArrayHasKey('regular', $availableFontStacks);
    self::assertArrayHasKey('sans-serif', $availableFontStacks);
    self::assertArrayHasKey('dyslexic', $availableFontStacks);
    self::assertSame('Regular', $availableFontStacks['regular']->getLabel());
});

test('unknown key is rejected', function () {
    $this->expectException(UnknownValueObjectKeyException::class);

    PreferredFontStack::makeFromKey('mono');
});
