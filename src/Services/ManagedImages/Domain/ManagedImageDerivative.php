<?php

declare(strict_types=1);

namespace App\Services\ManagedImages\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\ManagedFiles\Domain\ManagedFile;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ManagedImageDerivative extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: ManagedImage::class, inversedBy: 'derivatives')]
        #[ORM\JoinColumn(nullable: false)]
        private ManagedImage $parentImage,

        #[ORM\ManyToOne(targetEntity: ManagedImageDerivativeFormat::class)]
        #[ORM\JoinColumn(nullable: false)]
        private readonly ManagedImageDerivativeFormat $format,

        #[ORM\ManyToOne(targetEntity: ManagedFile::class)]
        #[ORM\JoinColumn(nullable: false)]
        private ManagedFile $derivative,
        ?int $id = null,
    ) {
        parent::__construct($id);
    }

    public function setParentImage(ManagedImage $parentImage): void
    {
        $this->parentImage = $parentImage;
    }

    public function getParentImage(): ManagedImage
    {
        return $this->parentImage;
    }

    public function getDerivativeFormat(): ManagedImageDerivativeFormat
    {
        return $this->format;
    }

    public function getDerivative(): ManagedFile
    {
        return $this->derivative;
    }

    public function setDerivative(ManagedFile $derivative): void
    {
        $this->derivative = $derivative;
    }
}
