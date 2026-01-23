<?php

declare(strict_types=1);

namespace App\Services\Files\Service\Interfaces;

use App\Services\Files\Domain\ManagedFile;
use App\Services\User\Domain\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface FilesServiceInterface
{
    public function save(ManagedFile $managedFile): ManagedFile;

    public function delete(ManagedFile $managedFile): void;

    public function find(int $id): ?ManagedFile;

    /**
     * @param int $firstResult
     * @param int $maxResults
     * @return ManagedFile[]
     */
    public function findAllPaginated(int $firstResult, int $maxResults = 50): iterable;

    public function getFilesCount(): int;

    public function uploadFile(
        UploadedFile $file,
        string $title,
        string $description,
        User $uploader,
        ? string $destination = null,
        ?ManagedFile $fileToReplace = null
    ): ManagedFile;
}
