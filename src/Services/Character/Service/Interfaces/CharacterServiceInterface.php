<?php

declare(strict_types=1);

namespace App\Services\Character\Service\Interfaces;

use App\Services\Character\Domain\Character;
use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;

interface CharacterServiceInterface extends DomainEntityServiceInterface
{
    public function save(Character $character, bool $flush = true): Character;

    public function find(int $id): ?Character;

    public function delete(Character $character, bool $flush = true): void;
}
