<?php

declare(strict_types=1);

namespace App\Services\World\Service\Interfaces;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\World\Domain\DTOs\WorldDateDto;
use App\Services\World\Domain\World;

interface WorldServiceInterface extends DomainEntityServiceInterface
{
    public function saveWorld(World $world, bool $flush = true): World;

    public function timeStampToWorldDate(int $timestamp, World $world): WorldDateDto;

    public function worldDateToTimestamp(WorldDateDto $worldDate, World $world): int;

    public function findBySlug(string $slug): ?World;
}
