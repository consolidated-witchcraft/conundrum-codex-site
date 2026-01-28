<?php

declare(strict_types=1);

namespace App\Services\Files\Service;

use App\Services\Files\Domain\Exceptions\BlankFinalPathException;
use App\Services\Files\Domain\Exceptions\GeneralFileManagementException;
use App\Services\Files\Domain\Exceptions\MissingUploadPrefixException;
use App\Services\Files\Domain\Exceptions\UndeterminedMimeTypeException;
use App\Services\Files\Domain\ManagedFile;
use App\Services\Files\Infrastructure\ManagedFileRepository;
use App\Services\Files\Service\Interfaces\FilesServiceInterface;
use App\Services\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

readonly class FilesService implements FilesServiceInterface
{
    public function __construct(
        private ManagedFileRepository $managedFileRepository,
        private EntityManagerInterface $entityManager,
        private SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads')]
        private string $publicFilesDirectory,
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

    /**
     * @param int $firstResult
     * @param int $maxResults
     * @return ManagedFile[]
     */
    public function findAllPaginated(int $firstResult, int $maxResults = 50): iterable
    {
        return $this->managedFileRepository->findAllPaginated($firstResult, $maxResults);
    }

    public function getFilesCount(): int
    {
        return $this->managedFileRepository->getFilesCount();
    }

    /**
     * @param UploadedFile $file
     * @param string $title
     * @param string $description
     * @param User $uploader
     * @param string|null $destination
     * @param ManagedFile|null $fileToReplace
     * @return ManagedFile
     * @throws BlankFinalPathException
     * @throws GeneralFileManagementException
     * @throws MissingUploadPrefixException
     * @throws UndeterminedMimeTypeException
     */
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

        $decollidedFileName = strtolower(sprintf("%s-%s.%s", $safeFilename, uniqid(), $file->guessExtension()));

        if (!$destination) {
            $destination = $this->publicFilesDirectory;
        }
        try {
            $file->move($destination, $decollidedFileName);
        } catch (FileException $e) {
            throw new GeneralFileManagementException(
                $e->getMessage(),
                $e->getCode(),
                $e
            );
        }

        $finalPath =  sprintf("%s/%s", self::stripPublic($destination), $decollidedFileName);
        if ($finalPath === '/') {
            throw new BlankFinalPathException('File path was either blank or empty');
        }

        if (!is_string($mimeType) || $mimeType === '') {
            throw new UndeterminedMimeTypeException('Could not determine MIME type');
        }

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

    /**
     * @throws MissingUploadPrefixException
     */
    protected static function stripPublic(string $path): string {
        $returnString = strstr($path, '/uploads');
        if ($returnString === false) {
            throw new MissingUploadPrefixException("Missing upload prefix");
        }
        return $returnString;
    }
}
