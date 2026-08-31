<?php

declare(strict_types=1);

use App\Services\Character\Domain\Character;
use App\Services\Character\Service\Interfaces\CharacterServiceInterface;
use App\ValueResolver\CharacterResolver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

beforeEach(function () {
    $this->characterService = $this->createMock(CharacterServiceInterface::class);
    $this->resolver = new CharacterResolver(characterService: $this->characterService);
});

test('resolve finds a character from the route id', function () {
    $character = $this->createStub(Character::class);
    $request = new Request(attributes: ['characterId' => '42']);
    $argument = new ArgumentMetadata('character', Character::class, false, false, null, false);
    $this->characterService->expects(self::once())->method('find')->with(42)->willReturn($character);

    $resolved = $this->resolver->resolve(request: $request, argument: $argument);

    self::assertSame(expected: [$character], actual: $resolved);
});

test('resolve ignores arguments of another type', function () {
    $request = new Request(attributes: ['characterId' => '42']);
    $argument = new ArgumentMetadata('character', stdClass::class, false, false, null, false);
    $this->characterService->expects(self::never())->method('find');

    $resolved = $this->resolver->resolve(request: $request, argument: $argument);

    self::assertSame(expected: [], actual: $resolved);
});

test('resolve ignores a non-numeric route id', function () {
    $request = new Request(attributes: ['characterId' => 'invalid']);
    $argument = new ArgumentMetadata('character', Character::class, false, false, null, false);
    $this->characterService->expects(self::never())->method('find');

    $resolved = $this->resolver->resolve(request: $request, argument: $argument);

    self::assertSame(expected: [], actual: $resolved);
});

test('resolve returns no value when the character does not exist', function () {
    $request = new Request(attributes: ['characterId' => 42]);
    $argument = new ArgumentMetadata('character', Character::class, false, false, null, false);
    $this->characterService->expects(self::once())->method('find')->with(42)->willReturn(null);

    $resolved = $this->resolver->resolve(request: $request, argument: $argument);

    self::assertSame(expected: [], actual: $resolved);
});
