<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Core\Domain;

use App\Services\Core\Domain\AbstractValueObject;
use App\Services\Core\Domain\Exceptions\InvalidHandleException;
use PHPUnit\Framework\TestCase;

abstract class AbstractValueObjectTestCase extends TestCase
{

    abstract function generateTestValueObject(array $overrides = []): AbstractValueObject;

    public function testInvalidHandle(): void {
        $this->expectException(InvalidHandleException::class);
        $this->generateTestValueObject(['handle' => 'This handle has spaces and &^$% characters in it']);
    }

    public function testToString(): void {
        $testLabel = 'This is a test label';
        $testClass = $this->generateTestValueObject(['label' => $testLabel]);
        self::assertEquals($testLabel, (string) $testClass);
    }
}
