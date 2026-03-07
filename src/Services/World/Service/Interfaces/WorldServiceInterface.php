<?php

declare(strict_types=1);

namespace App\Services\World\Service\Interfaces;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\World\Domain\World;

interface WorldServiceInterface extends DomainEntityServiceInterface
{
    public function saveWorld(World $world, bool $flush = true): World;
}
