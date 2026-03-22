<?php

declare(strict_types=1);

namespace App\Dto\Admin\File;

use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\User\Domain\User;

class AdminFileDto
{
    final public function __construct(
        public ? string $path = null,
        public ?string $title = null,
        public ?string $mimeType = null,
        public ?string $description = null,
        public ? User $uploader = null
    ) {}

    public static function makeFromFile(ManagedFile $file): self {
        return new static(
            path: $file->getPath(),
            title: $file->getTitle(),
            mimeType: $file->getMimeType(),
            description: $file->getDescription(),
            uploader: $file->getUploader()
        );
    }
}
