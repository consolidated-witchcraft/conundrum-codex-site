<?php

declare(strict_types=1);

namespace App\Services\ManagedImages\Service;

use App\Services\ManagedImages\Domain\Enums\ManagedImageTypeEnum;
use App\Services\ManagedImages\Domain\ImageTypeFormatMap;
use App\Services\ManagedImages\Domain\ManagedImageDerivativeFormat;
use App\Services\ManagedImages\Infrastructure\ImageTypeFormatMapRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class ImageTypeFormatMapSynchronizer
{
    /**
     * @var array<string, list<string>>
     */
    private const array TYPE_TO_FORMAT_HANDLES = [
        ManagedImageTypeEnum::DEFAULT->value => [
            ManagedImageTypeEnum::DEFAULT->value,
            ManagedImageTypeEnum::THUMBNAIL->value,
        ],
        ManagedImageTypeEnum::AVATAR->value => [
            ManagedImageTypeEnum::AVATAR_THUMBNAIL->value,
            ManagedImageTypeEnum::AVATAR->value,
        ],
    ];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ImageTypeFormatMapRepository $imageTypeFormatMapRepository,
    ) {
    }

    public function sync(): int
    {
        $updatedMapsCount = 0;

        foreach (self::TYPE_TO_FORMAT_HANDLES as $managedImageType => $formatHandles) {
            $typeEnum = ManagedImageTypeEnum::from($managedImageType);
            $formats = $this->findFormats($formatHandles);

            $existingMap = $this->imageTypeFormatMapRepository->findOneByManagedImageType($typeEnum);
            if (!$existingMap instanceof ImageTypeFormatMap) {
                $this->entityManager->persist(new ImageTypeFormatMap(
                    managedImageType: $typeEnum,
                    formats: new ArrayCollection($formats),
                ));
                ++$updatedMapsCount;

                continue;
            }

            $mapUpdated = false;
            foreach ($existingMap->getFormats()->toArray() as $existingFormat) {
                if (
                    $existingFormat instanceof ManagedImageDerivativeFormat
                    && !in_array($existingFormat, $formats, true)
                ) {
                    $existingMap->removeFormat($existingFormat);
                    $mapUpdated = true;
                }
            }

            foreach ($formats as $format) {
                if ($existingMap->getFormats()->contains($format)) {
                    continue;
                }

                $existingMap->addFormat($format);
                $mapUpdated = true;
            }

            if ($mapUpdated) {
                ++$updatedMapsCount;
            }
        }

        $this->entityManager->flush();

        return $updatedMapsCount;
    }

    /**
     * @param list<string> $formatHandles
     * @return list<ManagedImageDerivativeFormat>
     */
    private function findFormats(array $formatHandles): array
    {
        $formats = [];
        foreach ($formatHandles as $formatHandle) {
            $format = $this->entityManager
                ->getRepository(ManagedImageDerivativeFormat::class)
                ->findOneBy(['handle' => $formatHandle]);

            if ($format instanceof ManagedImageDerivativeFormat) {
                $formats[] = $format;
            }
        }

        return $formats;
    }
}
