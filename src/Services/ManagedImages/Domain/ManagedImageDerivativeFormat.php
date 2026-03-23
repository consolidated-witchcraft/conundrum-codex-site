<?php

declare(strict_types=1);

namespace App\Services\ManagedImages\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(readOnly: true)]
class ManagedImageDerivativeFormat extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column(type: 'string', unique: true)]
        private readonly string $handle,

        #[ORM\Column]
        private readonly int $resolutionX,

        #[ORM\Column]
        private readonly int $quality,

        #[ORM\Column(nullable: true)]
        private readonly ?int $resolutionY = null,

        #[ORM\Column(options: ['default' => false])]
        private readonly bool $allowCrop = false,

        #[ORM\Column(options: ['default' => false])]
        private readonly bool $centreImage = false,
        ?int $id = null,
    ) {
        parent::__construct($id);
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function getResolutionX(): int
    {
        return $this->resolutionX;
    }

    public function getResolutionY(): ?int
    {
        return $this->resolutionY;
    }

    public function getQuality(): int
    {
        return $this->quality;
    }

    public function isAllowCrop(): bool
    {
        return $this->allowCrop;
    }

    public function isCentreImage(): bool
    {
        return $this->centreImage;
    }
}
