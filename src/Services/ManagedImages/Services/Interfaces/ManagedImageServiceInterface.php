<?php

declare(strict_types=1);

namespace App\Services\ManagedImages\Services\Interfaces;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\ManagedImages\Domain\ManagedImage;
use App\Services\User\Domain\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface ManagedImageServiceInterface extends DomainEntityServiceInterface
{
    public function save(ManagedImage $managedImage, bool $flush = true): ManagedImage;

    public function findBySourceImage(ManagedFile $managedFile): ?ManagedImage;

    public function migrateManagedFile(ManagedFile $managedFile): ManagedImage;

    public function uploadImage(
        UploadedFile $file,
        string $title,
        string $description,
        User $uploader,
        ?string $destination = null,
        ?ManagedImage $imageToReplace = null,
    ): ManagedImage;
}
