<?php

declare(strict_types=1);

namespace App\Services\Files\Service\Interfaces;

use App\Services\Files\Domain\ManagedFile;

interface FilesServiceInterface
{
    public function save(ManagedFile $managedFile): ManagedFile;

    public function delete(ManagedFile $managedFile): void;

    public function find(int $id): ?ManagedFile;
}
