<?php

declare(strict_types=1);

namespace App\Services\ManagedFiles\Service;

use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\ManagedFiles\Domain\Exceptions\BlankFinalPathException;
use App\Services\ManagedFiles\Domain\Exceptions\GeneralFileManagementException;
use App\Services\ManagedFiles\Domain\Exceptions\MissingUploadPrefixException;
use App\Services\ManagedFiles\Domain\Exceptions\UndeterminedMimeTypeException;
use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\ManagedFiles\Infrastructure\ManagedFileRepository;
use App\Services\ManagedFiles\Service\Interfaces\ManagedFileDeduplicationServiceInterface;
use App\Services\ManagedFiles\Service\Interfaces\ManagedFileServiceInterface;
use App\Services\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ManagedFileService extends AbstractDomainEntityService implements ManagedFileServiceInterface
{
    public function __construct(
        private readonly ManagedFileRepository $managedFileRepository,
        private readonly ManagedFileDeduplicationServiceInterface $managedFileDeduplicationService,
        private readonly SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads')]
        private readonly string $publicFilesDirectory,
        EntityManagerInterface $entityManager,
    ){
        parent::__construct($entityManager);
    }

    public function save(ManagedFile $managedFile, bool $flush = true): ManagedFile
    {
        $this->entityManager->persist($managedFile);
        if ($flush) {
            $this->entityManager->flush();
        }

        return $managedFile;
    }

    public function delete(ManagedFile $managedFile, bool $flush = true): void
    {
        $this->entityManager->remove($managedFile);
        if ($flush) {
            $this->entityManager->flush();
        }

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

        $absolutePath = sprintf('%s/%s', $destination, $decollidedFileName);
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
                uploader: $uploader,
                fileHash: $this->generateFileHash(filePath: $absolutePath),
            );
            return $this->save($managedFile);
        } else {
            $fileToReplace->setTitle($title);
            $fileToReplace->setDescription($description);
            $fileToReplace->setMimeType($mimeType);
            $fileToReplace->setPath($finalPath);
            $fileToReplace->setFileHash($this->generateFileHash(filePath: $absolutePath));
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

    /**
     * @param array<int, string> $mimeTypes
     * @return ManagedFile[]
     */
    public function findByMimeTypes(array $mimeTypes): iterable
    {
        return $this->managedFileRepository->findByMimeTypes($mimeTypes);
    }

    /**
     * @return ManagedFile[]
     */
    public function findAll(): iterable
    {
        return $this->managedFileRepository->findAll();
    }

    public function findOrphans(): iterable
    {
        return $this->managedFileRepository->findOrphans();
    }

    /**
     * @inheritDoc
     */
    public function findDuplicates(): iterable
    {
        return $this->managedFileRepository->findDuplicates();
    }

    public function deduplicateFiles(): int
    {
        return $this->managedFileDeduplicationService->deduplicateFiles();
    }

    public function generateFileHash(string $filePath): string
    {
        $hash = hash_file('sha256', $filePath);
        if ($hash === false) {
            throw new GeneralFileManagementException(sprintf('Could not hash file "%s".', $filePath));
        }

        return $hash;
    }

    public function findOneByFileHashAndUploader(string $fileHash, User $uploader): ?ManagedFile
    {
        return $this->managedFileRepository->findOneByFileHashAndUploader($fileHash, $uploader);
    }
}
