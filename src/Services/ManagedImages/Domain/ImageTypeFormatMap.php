<?php

declare(strict_types=1);

namespace App\Services\ManagedImages\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\ManagedImages\Domain\Enums\ManagedImageTypeEnum;
use App\Services\ManagedImages\Infrastructure\ImageTypeFormatMapRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageTypeFormatMapRepository::class, readOnly: true)]
class ImageTypeFormatMap extends AbstractDomainEntity
{
    /**
     * @param Collection<int, ManagedImageDerivativeFormat> $formats
     */
    public function __construct(
        #[ORM\Column(type: 'string', length: 255, unique: true, enumType: ManagedImageTypeEnum::class)]
        private readonly ManagedImageTypeEnum $managedImageType,

        #[ORM\ManyToMany(targetEntity: ManagedImageDerivativeFormat::class, fetch: 'EAGER')]
        #[ORM\JoinTable(name: 'image_type_format_map_derivative_format')]
        private readonly Collection $formats = new ArrayCollection(),
        ?int $id = null,
    ) {
        parent::__construct($id);
    }

    public function getManagedImageType(): ManagedImageTypeEnum
    {
        return $this->managedImageType;
    }

    /**
     * @return Collection<int, ManagedImageDerivativeFormat>
     */
    public function getFormats(): Collection
    {
        return $this->formats;
    }
}
