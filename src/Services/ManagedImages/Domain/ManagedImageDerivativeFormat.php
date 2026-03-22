<?php

declare(strict_types=1);

namespace App\Services\ManagedImages\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ManagedImageDerivativeFormat extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column(type: 'string', unique: true)]
        private readonly string $handle,

        #[ORM\Column]
        private readonly int $resolutionX,

        #[ORM\Column]
        private readonly int $resolutionY,

        #[ORM\Column]
        private readonly int $quality,
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

    public function getResolutionY(): int
    {
        return $this->resolutionY;
    }

    public function getQuality(): int
    {
        return $this->quality;
    }
}
