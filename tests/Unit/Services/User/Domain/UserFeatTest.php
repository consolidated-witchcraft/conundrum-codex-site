<?php

declare(strict_types=1);

namespace App\Tests\Unit\Services\User\Domain;

use App\Services\Core\Domain\Exceptions\InvalidHandleException;
use App\Services\User\Domain\UserFeat;
use App\Services\User\Domain\ValueObjects\Rarity;
use PHPUnit\Framework\TestCase;

class UserFeatTest extends TestCase
{

    /**
     * @throws InvalidHandleException
     */
    private function generateTestUserFeat(array $overrides = []): UserFeat {
        return new UserFeat(
            label: $overrides['label'] ?? 'Test Label',
            handle: $overrides['handle'] ?? 'TestHandle',
            description: $overrides['description'] ?? 'Test Description',
            iconClass: $overrides['iconClass'] ?? 'test-icon-class',
            rarity: $overrides['rarity'] ?? $this->createStub(Rarity::class),
            gamesMasterAwardable: $overrides['gamesMasterAwardable'] ?? false,
            id: $overrides['id'] ?? null
        );
    }

    /**
     * @throws InvalidHandleException
     */
    public function testConstruct(): void {
        $label = "Test Label";
        $handle = "TestHandle";
        $description = "Test Description";
        $iconClass = "TestIconClass";
        $rarity = $this->createStub(Rarity::class);
        $id = 123;
        $feat = new UserFeat(
            label: $label,
            handle: $handle,
            description: $description,
            iconClass: $iconClass,
            rarity: $rarity,
            gamesMasterAwardable: false,
            id: $id
        );
        $this->assertEquals($label, $feat->getLabel());
        $this->assertEquals($handle, $feat->getHandle());
        $this->assertEquals($description, $feat->getDescription());
        $this->assertEquals($iconClass, $feat->getIconClass());
        $this->assertEquals($rarity, $feat->getRarity());
        $this->assertFalse($feat->isGamesMasterAwardable());
        $this->assertEquals($id, $feat->getId());
    }

    /**
     * @throws InvalidHandleException
     */
    public function testSetLabel(): void {
        $newLabel = "Test Label 2";
        $feat = $this->generateTestUserFeat();
        $feat->setLabel($newLabel);
        $this->assertEquals($newLabel, $feat->getLabel());
    }

    /**
     * @throws InvalidHandleException
     */
    public function testSetDescription(): void {
        $newDescription = "Test Description 2";
        $feat = $this->generateTestUserFeat();
        $feat->setDescription($newDescription);
        $this->assertEquals($newDescription, $feat->getDescription());
    }

    /**
     * @throws InvalidHandleException
     */
    public function testSetIconClass(): void {
        $newIconClass = "test-icon-class-2";
        $feat = $this->generateTestUserFeat();
        $feat->setIconClass($newIconClass);
        $this->assertEquals($newIconClass, $feat->getIconClass());
    }

    /**
     * @throws InvalidHandleException
     */
    public function testSetRarity(): void {
        $newRarity = $this->createConfiguredStub(Rarity::class, [
            'getKey' => 'TESTHANDLE2'
        ]);
        $feat = $this->generateTestUserFeat();
        $feat->setRarity($newRarity);
        $this->assertEquals($newRarity, $feat->getRarity());
    }

    public function testSetGamesMasterAwardable(): void {
        $feat = $this->generateTestUserFeat();
        self::assertFalse($feat->isGamesMasterAwardable());
        $feat->setGamesMasterAwardable(true);
        self::assertTrue($feat->isGamesMasterAwardable());
    }

    public function testHasDescription(): void {
        $feat = $this->generateTestUserFeat();
        self::assertTrue($feat->hasDescription());
    }
}
