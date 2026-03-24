<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Services\ManagedImages\Domain\ManagedImageDerivativeFormat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ManagedImageDerivativeFormatFixtures extends Fixture
{
    /**
     * @var array<string, array{resolution_x: int, resolution_y: int|null, quality: int, allow_crop: bool, centre_image: bool}>
     */
    private const array FORMATS = [
        'default' => [
            'resolution_x' => 1024,
            'resolution_y' => null,
            'quality' => 85,
            'allow_crop' => false,
            'centre_image' => false,
        ],
        'thumbnail' => [
            'resolution_x' => 256,
            'resolution_y' => null,
            'quality' => 85,
            'allow_crop' => false,
            'centre_image' => false,
        ],
        'avatar' => [
            'resolution_x' => 512,
            'resolution_y' => 512,
            'quality' => 85,
            'allow_crop' => true,
            'centre_image' => true,
        ],
        'avatar-thumbnail' => [
            'resolution_x' => 128,
            'resolution_y' => 128,
            'quality' => 75,
            'allow_crop' => true,
            'centre_image' => true,
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        /** @var \Doctrine\Persistence\ObjectRepository<ManagedImageDerivativeFormat> $repository */
        $repository = $manager->getRepository(ManagedImageDerivativeFormat::class);

        foreach (self::FORMATS as $handle => $definition) {
            $existingFormat = $repository->findOneBy(['handle' => $handle]);
            if ($existingFormat instanceof ManagedImageDerivativeFormat) {
                continue;
            }

            $manager->persist(new ManagedImageDerivativeFormat(
                handle: $handle,
                resolutionX: $definition['resolution_x'],
                quality: $definition['quality'],
                resolutionY: $definition['resolution_y'],
                allowCrop: $definition['allow_crop'],
                centreImage: $definition['centre_image'],
            ));
        }

        $manager->flush();
    }
}
