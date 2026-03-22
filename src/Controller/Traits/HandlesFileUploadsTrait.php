<?php

declare(strict_types=1);

namespace App\Controller\Traits;

use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\ManagedFiles\Service\Interfaces\ManagedFileServiceInterface;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\ManagedImages\Services\Interfaces\ManagedImageServiceInterface;
use App\Services\User\Domain\User;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait HandlesFileUploadsTrait
{
    protected function handleFileUpload(
        UploadedFile $uploadedFile,
        string $destination,
        ?ManagedFile $fileToReplace = null,
    ): ManagedFile {
        if (!isset($this->filesService) || ! $this->filesService instanceof ManagedFileServiceInterface) {
            throw new RuntimeException('You must set a managed file service');
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new RuntimeException('Invalid user class');
        }

        return $this->filesService->uploadFile(
            file: $uploadedFile,
            title: sprintf("%s' upload", $user->getUserName()),
            description: sprintf("%s' avatar", $user->getUserName()),
            uploader: $user,
            destination: $destination,
            fileToReplace: $fileToReplace,
        );
    }

    protected function handleImageUpload(
        UploadedFile $uploadedFile,
        string $destination,
        ?ManagedImage $imageToReplace = null,
    ): ManagedImage {
        if (!isset($this->managedImageService) || !$this->managedImageService instanceof ManagedImageServiceInterface) {
            throw new RuntimeException('You must set a managed image service');
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new RuntimeException('Invalid user class');
        }

        return $this->managedImageService->uploadImage(
            file: $uploadedFile,
            title: sprintf("%s' upload", $user->getUserName()),
            description: sprintf("%s' avatar", $user->getUserName()),
            uploader: $user,
            destination: $destination,
            imageToReplace: $imageToReplace,
        );
    }

    protected function stripPublic(string $path): string {

        return strstr($path, '/uploads');

    }
}
