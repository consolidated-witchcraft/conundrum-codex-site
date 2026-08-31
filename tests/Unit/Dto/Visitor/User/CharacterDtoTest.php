<?php

declare(strict_types=1);

use App\Dto\Visitor\User\CharacterDto;
use App\Services\Character\Domain\Character;

test('make from character maps editable fields', function () {
    $character = $this->createConfiguredStub(Character::class, [
        'getName' => 'Seraphina Vale',
        'getClass' => 'Wizard',
        'getRace' => 'Human',
        'getLevel' => 7,
        'getBiography' => 'Keeper of the celestial archive.',
    ]);

    $dto = CharacterDto::makeFromCharacter(character: $character);

    self::assertSame(expected: 'Seraphina Vale', actual: $dto->name);
    self::assertSame(expected: 'Wizard', actual: $dto->class);
    self::assertSame(expected: 'Human', actual: $dto->race);
    self::assertSame(expected: 7, actual: $dto->level);
    self::assertSame(expected: 'Keeper of the celestial archive.', actual: $dto->biography);
    self::assertNull(actual: $dto->portrait);
    self::assertNull(actual: $dto->characterSheet);
});
