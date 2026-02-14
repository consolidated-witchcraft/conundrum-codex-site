<?php

namespace App\Services\Core\Domain\Interfaces;

interface DomainEntityInterface
{
    public function getCreatedAt(): \DateTimeImmutable;

    public function getUpdatedAt(): \DateTimeImmutable;
}
