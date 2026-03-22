<?php

declare(strict_types=1);

namespace App\Services\ManagedImages\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\ManagedFiles\Domain\ManagedFile;
use App\Services\ManagedImages\Domain\Exceptions\DuplicateManagedImageDerivativeFormatException;
use App\Services\ManagedImages\Infrastructure\ManagedImageRepository;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ManagedImageRepository::class)]
class ManagedImage extends AbstractDomainEntity
{
    /**
     * @param Collection<int, ManagedImageDerivative> $derivatives
     */
    public function __construct(
        #[ORM\Column(type: 'string', length: 255)]
        private string $description,

        #[ORM\ManyToOne(targetEntity: User::class)]
        #[ORM\JoinColumn(nullable: false)]
        private readonly User $createdBy,

        #[ORM\OneToOne(targetEntity: ManagedFile::class)]
        #[ORM\JoinColumn(nullable: false)]
        private ManagedFile $sourceImage,

        #[ORM\OneToMany(targetEntity: ManagedImageDerivative::class, mappedBy: 'parentImage')]
        private Collection $derivatives = new ArrayCollection(),
        ?int $id = null,
    ) {
        parent::__construct($id);
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPath(): string
    {
        return $this->sourceImage->getPath();
    }

    public function getMimeType(): string
    {
        return $this->sourceImage->getMimeType();
    }

    public function getTitle(): string
    {
        return $this->sourceImage->getTitle();
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function getSourceImage(): ManagedFile
    {
        return $this->sourceImage;
    }

    public function setSourceImage(ManagedFile $sourceImage): void
    {
        $this->sourceImage = $sourceImage;
    }

    /**
     * @throws DuplicateManagedImageDerivativeFormatException
     */
    public function addDerivative(ManagedImageDerivative $derivative): void
    {
        $formatHandle = $derivative->getDerivativeFormat()->getHandle();

        if (null !== $this->getDerivative($formatHandle)) {
            throw new DuplicateManagedImageDerivativeFormatException($formatHandle);
        }

        $derivative->setParentImage($this);
        $this->derivatives->add($derivative);
    }

    public function getDerivative(string $derivativeFormatHandle): ?ManagedImageDerivative
    {
        foreach ($this->derivatives as $derivative) {
            if ($derivative->getDerivativeFormat()->getHandle() === $derivativeFormatHandle) {
                return $derivative;
            }
        }

        return null;
    }
}
