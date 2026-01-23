<?php

declare(strict_types=1);

namespace App\Controller\Traits;
use App\Services\Files\Domain\ManagedFile;
use App\Services\User\Domain\User;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
trait HandlesImageUploadsTrait
{

    protected function handleImageUpload(
        UploadedFile $imageFile,
        string $destination
    ): ManagedFile {
        $mimeType = ($imageFile->getMimeType());
        if ($this->slugger === null) {
            throw new RuntimeException('You must set a valid slugger');
        }

        if ($this->filesService === null) {
            throw new RuntimeException('You must set a managed file service');
        }
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);

        $imageFileName = strtolower(sprintf("%s-%s.%s", $safeFilename, uniqid(), $imageFile->guessExtension()));


        try {
            $imageFile->move($destination, $imageFileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        $finalPath =  sprintf("%s/%s", $this->stripPublic($destination), $imageFileName);
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw new RuntimeException('Invalid user class');
        }
        $managedFile = new ManagedFile(
            path: $finalPath,
            mimeType: $mimeType,
            title: sprintf("%s' upload", $user->getUserName()),
            description: sprintf("%s' avatar", $user->getUserName()),
            uploader: $user
        );
        $this->filesService->save($managedFile);
        return $managedFile;
    }

    protected function stripPublic(string $path): string {

        return strstr($path, '/uploads');

    }
}
