<?php

declare(strict_types=1);

namespace App\Services\ManagedImages\Services;

use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\ManagedFiles\Service\Interfaces\ManagedFileServiceInterface;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\ManagedImages\Infrastructure\ManagedImageRepository;
use App\Services\ManagedImages\Services\Interfaces\ManagedImageServiceInterface;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ManagedImageService extends AbstractDomainEntityService implements ManagedImageServiceInterface
{
    public function __construct(
        private readonly ManagedImageRepository $managedImageRepository,
        private readonly ManagedFileServiceInterface $managedFileService,
        EntityManagerInterface $entityManager,
    ) {
        parent::__construct($entityManager);
    }

    public function save(ManagedImage $managedImage, bool $flush = true): ManagedImage
    {
        $this->entityManager->persist($managedImage);
        if ($flush) {
            $this->entityManager->flush();
        }

        return $managedImage;
    }

    public function findBySourceImage(ManagedFile $managedFile): ?ManagedImage
    {
        return $this->managedImageRepository->findOneBySourceImage(sourceImage: $managedFile);
    }

    public function migrateManagedFile(ManagedFile $managedFile): ManagedImage
    {
        $existingManagedImage = $this->findBySourceImage(managedFile: $managedFile);
        if ($existingManagedImage instanceof ManagedImage) {
            return $existingManagedImage;
        }

        $managedImage = new ManagedImage(
            description: $managedFile->getDescription(),
            createdBy: $managedFile->getUploader(),
            sourceImage: $managedFile,
            derivatives: new ArrayCollection(),
        );

        return $this->save(managedImage: $managedImage);
    }

    public function uploadImage(
        UploadedFile $file,
        string $title,
        string $description,
        User $uploader,
        ?string $destination = null,
        ?ManagedImage $imageToReplace = null,
    ): ManagedImage {
        $managedFile = $this->managedFileService->uploadFile(
            file: $file,
            title: $title,
            description: $description,
            uploader: $uploader,
            destination: $destination,
            fileToReplace: $imageToReplace?->getSourceImage(),
        );

        return $this->migrateManagedFile(managedFile: $managedFile);
    }
}
