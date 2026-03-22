<?php

declare(strict_types=1);

namespace App\Services\ManagedFiles\Service;

use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\ManagedFiles\Infrastructure\ManagedFileRepository;
use App\Services\ManagedFiles\Service\Interfaces\ManagedFileDeduplicationServiceInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class ManagedFileDeduplicationService implements ManagedFileDeduplicationServiceInterface
{
    public function __construct(
        private ManagedFileRepository $managedFileRepository,
        #[Autowire('%kernel.project_dir%/public/uploads')]
        private string $publicFilesDirectory,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function deduplicateFiles(): int
    {
        $deletedFilesCount = 0;

        foreach ($this->managedFileRepository->findDuplicateGroups() as $group) {
            $duplicateFiles = $this->managedFileRepository->findDuplicateGroup(
                uploaderId: $group['uploaderId'],
                path: $group['path'],
                fileHash: $group['fileHash'],
            );

            if (count($duplicateFiles) < 2) {
                continue;
            }

            $canonicalFile = $duplicateFiles[0];
            $absolutePath = $this->resolveAbsolutePath(relativePath: $canonicalFile->getPath());
            if (!is_file($absolutePath) || !is_readable($absolutePath)) {
                continue;
            }

            $mimeType = $this->detectMimeType(absolutePath: $absolutePath);
            if ($mimeType === null) {
                continue;
            }

            $replacementFile = new ManagedFile(
                path: $canonicalFile->getPath(),
                mimeType: $mimeType,
                title: $canonicalFile->getTitle(),
                description: $canonicalFile->getDescription(),
                uploader: $canonicalFile->getUploader(),
                fileHash: $canonicalFile->getFileHash(),
            );

            $this->entityManager->persist($replacementFile);
            $this->repointManagedFileReferences(
                duplicateFiles: $duplicateFiles,
                replacementFile: $replacementFile,
            );

            foreach ($duplicateFiles as $duplicateFile) {
                $this->entityManager->remove($duplicateFile);
            }

            $this->entityManager->flush();
            $deletedFilesCount += count($duplicateFiles);
        }

        return $deletedFilesCount;
    }

    /**
     * @param ManagedFile[] $duplicateFiles
     */
    private function repointManagedFileReferences(array $duplicateFiles, ManagedFile $replacementFile): void
    {
        $metadataFactory = $this->entityManager->getMetadataFactory();

        foreach ($metadataFactory->getAllMetadata() as $metadata) {
            foreach ($metadata->getAssociationNames() as $fieldName) {
                $mapping = $metadata->getAssociationMapping($fieldName);

                if ($mapping->targetEntity !== ManagedFile::class || !$mapping->isOwningSide()) {
                    continue;
                }

                $entities = $this->entityManager->getRepository($metadata->getName())->findAll();
                foreach ($entities as $entity) {
                    $updated = match ($mapping->type()) {
                        ClassMetadata::MANY_TO_MANY => $this->replaceManagedFileInCollectionAssociation(
                            entity: $entity,
                            fieldName: $fieldName,
                            duplicateFiles: $duplicateFiles,
                            replacementFile: $replacementFile,
                        ),
                        ClassMetadata::ONE_TO_ONE, ClassMetadata::MANY_TO_ONE => $this->replaceManagedFileInSingleAssociation(
                            entity: $entity,
                            fieldName: $fieldName,
                            duplicateFiles: $duplicateFiles,
                            replacementFile: $replacementFile,
                        ),
                        default => false,
                    };

                    if ($updated) {
                        $this->entityManager->persist($entity);
                    }
                }
            }
        }
    }

    /**
     * @param ManagedFile[] $duplicateFiles
     */
    private function replaceManagedFileInSingleAssociation(
        object $entity,
        string $fieldName,
        array $duplicateFiles,
        ManagedFile $replacementFile,
    ): bool {
        $currentValue = $this->readPropertyValue(entity: $entity, fieldName: $fieldName);
        if (!$currentValue instanceof ManagedFile || !in_array($currentValue, $duplicateFiles, true)) {
            return false;
        }

        $setterName = 'set'.ucfirst($fieldName);
        if (method_exists($entity, $setterName)) {
            $entity->{$setterName}($replacementFile);

            return true;
        }

        $this->writePropertyValue(entity: $entity, fieldName: $fieldName, value: $replacementFile);

        return true;
    }

    /**
     * @param ManagedFile[] $duplicateFiles
     */
    private function replaceManagedFileInCollectionAssociation(
        object $entity,
        string $fieldName,
        array $duplicateFiles,
        ManagedFile $replacementFile,
    ): bool {
        $collection = $this->readPropertyValue(entity: $entity, fieldName: $fieldName);
        if (!$collection instanceof Collection) {
            return false;
        }

        $updated = false;
        foreach ($duplicateFiles as $duplicateFile) {
            if ($collection->contains($duplicateFile)) {
                $collection->removeElement($duplicateFile);
                $updated = true;
            }
        }

        if ($updated && !$collection->contains($replacementFile)) {
            $collection->add($replacementFile);
        }

        return $updated;
    }

    private function resolveAbsolutePath(string $relativePath): string
    {
        return dirname($this->publicFilesDirectory).$relativePath;
    }

    private function detectMimeType(string $absolutePath): ?string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($absolutePath);

        return is_string($mimeType) && $mimeType !== '' ? $mimeType : null;
    }

    private function readPropertyValue(object $entity, string $fieldName): mixed
    {
        $reflectionProperty = $this->getReflectionProperty(entity: $entity, fieldName: $fieldName);

        return $reflectionProperty->getValue($entity);
    }

    private function writePropertyValue(object $entity, string $fieldName, mixed $value): void
    {
        $reflectionProperty = $this->getReflectionProperty(entity: $entity, fieldName: $fieldName);
        $reflectionProperty->setValue($entity, $value);
    }

    private function getReflectionProperty(object $entity, string $fieldName): \ReflectionProperty
    {
        $reflectionClass = new \ReflectionObject($entity);

        while (!$reflectionClass->hasProperty($fieldName)) {
            $reflectionClass = $reflectionClass->getParentClass();
            if ($reflectionClass === false) {
                throw new \RuntimeException(sprintf(
                    'Property "%s" was not found on "%s".',
                    $fieldName,
                    $entity::class
                ));
            }
        }

        return $reflectionClass->getProperty($fieldName);
    }
}
