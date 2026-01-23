<?php

declare(strict_types=1);

namespace App\Services\Files\Service;

use App\Services\Files\Domain\ManagedFile;
use App\Services\Files\Infrastructure\ManagedFileRepository;
use App\Services\Files\Service\Interfaces\FilesServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

class FilesService implements FilesServiceInterface
{
    public function __construct(
        private readonly ManagedFileRepository $managedFileRepository,
        private readonly EntityManagerInterface $entityManager
    ){}

    public function save(ManagedFile $managedFile): ManagedFile
    {
        $this->entityManager->persist($managedFile);
        $this->entityManager->flush();
        return $managedFile;
    }

    public function delete(ManagedFile $managedFile): void
    {
        // TODO: Implement delete() method.
    }

    public function find(int $id): ?ManagedFile
    {
        // TODO: Implement find() method.
    }
}
