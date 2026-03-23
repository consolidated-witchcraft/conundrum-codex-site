<?php

declare(strict_types=1);

namespace App\Services\ManagedImages\Services;

use App\Message\ManagedImageVariantsUpdateMessage;
use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\ManagedFiles\Service\Interfaces\ManagedFileServiceInterface;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\ManagedImages\Domain\ManagedImageDerivative;
use App\Services\ManagedImages\Domain\ManagedImageDerivativeFormat;
use App\Services\ManagedImages\Domain\Enums\ManagedImageTypeEnum;
use App\Services\ManagedImages\Infrastructure\ManagedImageRepository;
use App\Services\ManagedImages\Services\Exceptions\ManagedFileNotImageException;
use App\Services\ManagedImages\Services\Interfaces\ManagedImageServiceInterface;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\MessageBusInterface;

class ManagedImageService extends AbstractDomainEntityService implements ManagedImageServiceInterface
{
    private const string DERIVATIVE_IMAGE_FORMAT = 'webp';
    private const string DERIVATIVE_MIME_TYPE = 'image/webp';

    public function __construct(
        private readonly ManagedImageRepository $managedImageRepository,
        private readonly ManagedFileServiceInterface $managedFileService,
        private readonly MessageBusInterface $messageBus,
        #[Autowire('%kernel.project_dir%/public/uploads')]
        private readonly string $publicFilesDirectory,
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

    public function generateMissingVariants(ManagedImage $managedImage, array $missingFormats): void
    {
        if ($missingFormats === []) {
            return;
        }

        $sourceImage = $managedImage->getSourceImage();
        if (!$sourceImage->isImage()) {
            throw new ManagedFileNotImageException('Managed file is not an image.');
        }

        $sourceImagePath = $this->resolveAbsolutePath($sourceImage->getPath());
        foreach ($missingFormats as $format) {
            $derivativeFile = $this->createDerivativeManagedFile($managedImage, $format, $sourceImagePath);
            $derivative = new ManagedImageDerivative(
                parentImage: $managedImage,
                format: $format,
                derivative: $derivativeFile,
            );

            $managedImage->addDerivative($derivative);
            $this->entityManager->persist($derivativeFile);
            $this->entityManager->persist($derivative);
        }

        $this->entityManager->flush();
    }

    public function uploadImage(
        UploadedFile $file,
        string $title,
        string $description,
        User $uploader,
        ManagedImageTypeEnum $type = ManagedImageTypeEnum::DEFAULT,
        ?string $destination = null,
        ?ManagedImage $imageToReplace = null,
    ): ManagedImage {
        $existingManagedImage = $this->findExistingManagedImageForUpload($file, $uploader);
        if ($existingManagedImage instanceof ManagedImage) {
            $existingManagedImage->setType($type);

            return $existingManagedImage;
        }

        $managedFile = $this->managedFileService->uploadFile(
            file: $file,
            title: $title,
            description: $description,
            uploader: $uploader,
            destination: $destination,
            fileToReplace: $imageToReplace?->getSourceImage(),
        );

        $managedImage = $this->migrateManagedFile(managedFile: $managedFile);
        $managedImage->setType($type);

        if ($imageToReplace instanceof ManagedImage) {
            $this->invalidateDerivatives($managedImage);
        }

        if ($managedImage->getId() !== null) {
            $this->messageBus->dispatch(new ManagedImageVariantsUpdateMessage($managedImage->getId()));
        }

        return $managedImage;
    }

    private function findExistingManagedImageForUpload(UploadedFile $file, User $uploader): ?ManagedImage
    {
        $fileHash = $this->managedFileService->generateFileHash($file->getPathname());
        $existingManagedFile = $this->managedFileService->findOneByFileHashAndUploader($fileHash, $uploader);
        if (!$existingManagedFile instanceof ManagedFile) {
            return null;
        }

        return $this->migrateManagedFile($existingManagedFile);
    }

    private function createDerivativeManagedFile(
        ManagedImage $managedImage,
        ManagedImageDerivativeFormat $format,
        string $sourceImagePath,
    ): ManagedFile {
        $imagick = new \Imagick($sourceImagePath);

        try {
            $this->transformImage($imagick, $format);
            $imagick->setImageFormat(self::DERIVATIVE_IMAGE_FORMAT);

            $targetAbsolutePath = $this->buildDerivativeAbsolutePath($managedImage, $format);
            $targetDirectory = dirname($targetAbsolutePath);
            if (!is_dir($targetDirectory)) {
                mkdir($targetDirectory, 0775, true);
            }

            $imagick->writeImage($targetAbsolutePath);

            return new ManagedFile(
                path: $this->toRelativeUploadPath($targetAbsolutePath),
                mimeType: self::DERIVATIVE_MIME_TYPE,
                title: sprintf('%s (%s)', $managedImage->getTitle(), $format->getHandle()),
                description: $managedImage->getDescription(),
                uploader: $managedImage->getCreatedBy(),
                fileHash: $this->managedFileService->generateFileHash($targetAbsolutePath),
            );
        } finally {
            $imagick->clear();
            $imagick->destroy();
        }
    }

    private function transformImage(\Imagick $imagick, ManagedImageDerivativeFormat $format): void
    {
        if ($format->isAllowCrop() && $format->getResolutionY() !== null) {
            $this->cropToResolution($imagick, $format);
        } else {
            $this->resizeToFit($imagick, $format);
        }

        $imagick->setImageCompressionQuality($format->getQuality());
        $imagick->stripImage();
    }

    private function resizeToFit(\Imagick $imagick, ManagedImageDerivativeFormat $format): void
    {
        $targetWidth = $format->getResolutionX();
        $targetHeight = $format->getResolutionY();

        if ($targetHeight === null) {
            $imagick->thumbnailImage($targetWidth, 0);

            return;
        }

        if ($targetWidth === 0) {
            $imagick->thumbnailImage(0, $targetHeight);

            return;
        }

        $imagick->thumbnailImage($targetWidth, $targetHeight, true);
    }

    private function cropToResolution(\Imagick $imagick, ManagedImageDerivativeFormat $format): void
    {
        $targetWidth = $format->getResolutionX();
        $targetHeight = $format->getResolutionY();
        if ($targetHeight === null) {
            return;
        }

        $sourceWidth = $imagick->getImageWidth();
        $sourceHeight = $imagick->getImageHeight();
        $scale = max($targetWidth / $sourceWidth, $targetHeight / $sourceHeight);

        $resizedWidth = (int) ceil($sourceWidth * $scale);
        $resizedHeight = (int) ceil($sourceHeight * $scale);

        $imagick->resizeImage($resizedWidth, $resizedHeight, \Imagick::FILTER_LANCZOS, 1.0);

        $cropX = $format->isCentreImage() ? max(0, (int) floor(($resizedWidth - $targetWidth) / 2)) : 0;
        $cropY = $format->isCentreImage() ? max(0, (int) floor(($resizedHeight - $targetHeight) / 2)) : 0;

        $imagick->cropImage($targetWidth, $targetHeight, $cropX, $cropY);
        $imagick->setImagePage(0, 0, 0, 0);
    }

    private function resolveAbsolutePath(string $relativeUploadPath): string
    {
        return sprintf('%s%s', dirname($this->publicFilesDirectory), $relativeUploadPath);
    }

    private function invalidateDerivatives(ManagedImage $managedImage): void
    {
        $removedDerivatives = false;

        foreach ($managedImage->getDerivatives()->toArray() as $derivative) {
            if (!$derivative instanceof ManagedImageDerivative) {
                continue;
            }

            $derivativeFile = $derivative->getDerivative();
            $absoluteDerivativePath = $this->resolveAbsolutePath($derivativeFile->getPath());
            if (is_file($absoluteDerivativePath)) {
                unlink($absoluteDerivativePath);
            }

            $managedImage->removeDerivative($derivative);
            $this->entityManager->remove($derivative);
            $this->entityManager->remove($derivativeFile);
            $removedDerivatives = true;
        }

        if ($removedDerivatives) {
            $this->entityManager->flush();
        }
    }

    private function buildDerivativeAbsolutePath(ManagedImage $managedImage, ManagedImageDerivativeFormat $format): string
    {
        $sourcePathInfo = pathinfo($managedImage->getSourceImage()->getPath());
        $filename = sprintf(
            '%s-%s.%s',
            $sourcePathInfo['filename'] ?? 'managed-image',
            $format->getHandle(),
            self::DERIVATIVE_IMAGE_FORMAT,
        );

        return sprintf('%s/variants/%s', $this->publicFilesDirectory, $filename);
    }

    private function toRelativeUploadPath(string $absolutePath): string
    {
        return sprintf(
            '/uploads/%s',
            ltrim(substr($absolutePath, strlen($this->publicFilesDirectory)), '/'),
        );
    }
}
