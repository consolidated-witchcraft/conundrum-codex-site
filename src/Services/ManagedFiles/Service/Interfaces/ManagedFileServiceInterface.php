<?php

declare(strict_types=1);

namespace App\Services\ManagedFiles\Service\Interfaces;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ManagedFileServiceInterface extends DomainEntityServiceInterface
{
    public function save(ManagedFile $managedFile, bool $flush = true): ManagedFile;

    public function delete(ManagedFile $managedFile, bool $flush = true): void;

    public function find(int $id): ?ManagedFile;

    /**
     * @return ManagedFile[]
     */
    public function findAll(): iterable;

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

    /**
     * @param array $mimeTypes
     * @return ManagedFile[]
     */
    public function findByMimeTypes(array $mimeTypes): iterable;

    /**
     * @return ManagedFile[]
     */
    public function findOrphans(): iterable;

    /**
     * @return ManagedFile[]
     */
    public function findDuplicates(): iterable;

    public function deduplicateFiles(): int;

    public function generateFileHash(string $filePath): string;

    public function findOneByFileHashAndUploader(string $fileHash, User $uploader): ?ManagedFile;
}
