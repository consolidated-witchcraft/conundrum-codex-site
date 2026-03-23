<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Services\ManagedImages\Domain\Enums\ManagedImageTypeEnum;
use App\Services\ManagedImages\Domain\ImageTypeFormatMap;
use App\Services\ManagedImages\Domain\ManagedImageDerivativeFormat;
use App\Services\ManagedImages\Infrastructure\ImageTypeFormatMapRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;

class ManagedImageTypeFormatMapFixtures extends Fixture implements DependentFixtureInterface
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

    public function load(ObjectManager $manager): void
    {
        /** @var ImageTypeFormatMapRepository $imageTypeFormatMapRepository */
        $imageTypeFormatMapRepository = $manager->getRepository(ImageTypeFormatMap::class);

        foreach (self::TYPE_TO_FORMAT_HANDLES as $managedImageType => $formatHandles) {
            $typeEnum = ManagedImageTypeEnum::from($managedImageType);
            $formats = [];
            foreach ($formatHandles as $formatHandle) {
                $format = $manager->getRepository(ManagedImageDerivativeFormat::class)->findOneBy(['handle' => $formatHandle]);
                if ($format instanceof ManagedImageDerivativeFormat) {
                    $formats[] = $format;
                }
            }

            $existingMap = $imageTypeFormatMapRepository->findOneByManagedImageType($typeEnum);
            if (!$existingMap instanceof ImageTypeFormatMap) {
                $manager->persist(new ImageTypeFormatMap(
                    managedImageType: $typeEnum,
                    formats: new ArrayCollection($formats),
                ));

                continue;
            }

            foreach ($existingMap->getFormats()->toArray() as $existingFormat) {
                if (
                    $existingFormat instanceof ManagedImageDerivativeFormat
                    && !in_array($existingFormat, $formats, true)
                ) {
                    $existingMap->removeFormat($existingFormat);
                }
            }

            foreach ($formats as $format) {
                $existingMap->addFormat($format);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ManagedImageDerivativeFormatFixtures::class,
        ];
    }
}
