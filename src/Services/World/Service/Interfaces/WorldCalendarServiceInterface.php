<?php

declare(strict_types=1);

namespace App\Services\World\Service\Interfaces;

use App\Services\World\Domain\DTOs\WorldDateDto;
use App\Services\World\Domain\World;

interface WorldCalendarServiceInterface
{
    public function timeStampToWorldDate(int $timestamp, World $world): WorldDateDto;

    public function worldDateToTimestamp(WorldDateDto $worldDate, World $world): int;
}
