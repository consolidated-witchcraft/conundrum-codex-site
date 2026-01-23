<?php

declare(strict_types=1);

namespace App\Services\Files\Service;

use App\Services\Files\Domain\ManagedFile;
use App\Services\Files\Infrastructure\ManagedFileRepository;
use App\Services\Files\Service\Interfaces\FilesServiceInterface;
use App\Services\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FilesService implements FilesServiceInterface
{
    public function __construct(
        private readonly ManagedFileRepository $managedFileRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads')] private readonly string $publicFilesDirectory,
    ){}

    public function save(ManagedFile $managedFile): ManagedFile
    {
        $this->entityManager->persist($managedFile);
        $this->entityManager->flush();
        return $managedFile;
    }

    public function delete(ManagedFile $managedFile): void
    {
        $this->entityManager->remove($managedFile);
        $this->entityManager->flush();
    }

    public function find(int $id): ?ManagedFile
    {
        return $this->managedFileRepository->find($id);
    }

    public function findAllPaginated(int $firstResult, int $maxResults = 50): iterable
    {
        return $this->managedFileRepository->findAllPaginated($firstResult, $maxResults);
    }

    public function getFilesCount(): int
    {
        return $this->managedFileRepository->getFilesCount();
    }

    public function uploadFile(
        UploadedFile $file,
        string $title,
        string $description,
        User $uploader,
        ?string $destination = null,
        ?ManagedFile $fileToReplace = null
    ): ManagedFile {
        $mimeType = ($file->getMimeType());
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);

        $imageFileName = strtolower(sprintf("%s-%s.%s", $safeFilename, uniqid(), $file->guessExtension()));

        if (!$destination) {
            $destination = $this->publicFilesDirectory;
        }
        try {
            $file->move($destination, $imageFileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        $finalPath =  sprintf("%s/%s", self::stripPublic($destination), $imageFileName);

        if ($fileToReplace === null) {
            $managedFile = new ManagedFile(
                path: $finalPath,
                mimeType: $mimeType,
                title: $title,
                description: $description,
                uploader: $uploader
            );
            return $this->save($managedFile);
        } else {
            $fileToReplace->setTitle($title);
            $fileToReplace->setDescription($description);
            $fileToReplace->setMimeType($mimeType);
            $fileToReplace->setPath($finalPath);
            return $this->save($fileToReplace);
        }


    }

    protected static function stripPublic(string $path): string {
        return strstr($path, '/uploads');
    }
}
