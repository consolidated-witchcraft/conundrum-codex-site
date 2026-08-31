<?php

declare(strict_types=1);

use App\Services\Character\Domain\Character;
use App\Services\Character\Infrastructure\CharacterRepository;
use App\Services\Character\Service\CharacterService;
use Doctrine\ORM\EntityManagerInterface;

beforeEach(function () {
    $this->characterRepository = $this->createMock(CharacterRepository::class);
    $this->entityManager = $this->createMock(EntityManagerInterface::class);
    $this->characterService = new CharacterService(
        characterRepository: $this->characterRepository,
        entityManager: $this->entityManager,
    );
});

test('save persists and flushes a character', function () {
    $character = $this->createStub(Character::class);
    $this->entityManager->expects(self::once())->method('persist')->with($character);
    $this->entityManager->expects(self::once())->method('flush');

    $result = $this->characterService->save(character: $character);

    self::assertSame(expected: $character, actual: $result);
});

test('save can skip flushing', function () {
    $character = $this->createStub(Character::class);
    $this->entityManager->expects(self::once())->method('persist')->with($character);
    $this->entityManager->expects(self::never())->method('flush');

    $result = $this->characterService->save(character: $character, flush: false);

    self::assertSame(expected: $character, actual: $result);
});

test('find delegates to the character repository', function () {
    $character = $this->createStub(Character::class);
    $this->characterRepository->expects(self::once())->method('find')->with(42)->willReturn($character);

    $result = $this->characterService->find(id: 42);

    self::assertSame(expected: $character, actual: $result);
});

test('delete removes and flushes a character', function () {
    $character = $this->createStub(Character::class);
    $this->entityManager->expects(self::once())->method('remove')->with($character);
    $this->entityManager->expects(self::once())->method('flush');

    $this->characterService->delete(character: $character);
});

test('delete can skip flushing', function () {
    $character = $this->createStub(Character::class);
    $this->entityManager->expects(self::once())->method('remove')->with($character);
    $this->entityManager->expects(self::never())->method('flush');

    $this->characterService->delete(character: $character, flush: false);
});
