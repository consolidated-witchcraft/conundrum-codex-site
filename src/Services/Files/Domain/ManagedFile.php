<?php

declare(strict_types=1);

namespace App\Services\Files\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Files\Infrastructure\ManagedFileRepository;
use App\Services\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ManagedFileRepository::class)]
class ManagedFile extends AbstractDomainEntity
{

    public function __construct(
        #[ORM\Column(length: 1024)]
        private string $path,

        #[ORM\Column(length: 128)]
        private string $mimeType,

        #[ORM\Column(length: 255)]
        private string $title,

        #[ORM\Column(length: 512)]
        private string $description,

        #[ORM\ManyToOne(targetEntity: User::class)]
        private User $uploader,
        ?int $id = null,
    ) {
        parent::__construct($id);
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getUploader(): User {
        return $this->uploader;
    }

}
