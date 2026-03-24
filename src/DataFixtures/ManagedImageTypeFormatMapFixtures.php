<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Services\ManagedImages\Service\ImageTypeFormatMapSynchronizer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ManagedImageTypeFormatMapFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly ImageTypeFormatMapSynchronizer $imageTypeFormatMapSynchronizer,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->imageTypeFormatMapSynchronizer->sync();
    }

    public function getDependencies(): array
    {
        return [
            ManagedImageDerivativeFormatFixtures::class,
        ];
    }
}
