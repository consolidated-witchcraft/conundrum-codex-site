<?php

declare(strict_types=1);

namespace App\Services\ManagedFiles\Service\Interfaces;

interface ManagedFileDeduplicationServiceInterface
{
    public function deduplicateFiles(): int;
}
