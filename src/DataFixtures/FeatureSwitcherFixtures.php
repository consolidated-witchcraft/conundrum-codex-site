<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Services\FeatureSwitcher\Domain\FeatureSwitcher;
use App\Services\FeatureSwitcher\Infrastructure\FeatureSwitcherRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FeatureSwitcherFixtures extends Fixture
{
    private const array FEATURE_SWITCHERS = [
        'EVENTS_SYSTEM' => "Events System."
    ];

    public function load(ObjectManager $manager): void
    {
        /**
         * @var FeatureSwitcherRepository $repository
         */
        $repository = $manager->getRepository(FeatureSwitcher::class);
        foreach (self::FEATURE_SWITCHERS as $handle => $label) {
            if (null === $repository->findByHandle($handle)) {
                $manager->persist(new FeatureSwitcher(handle: $handle, label: $label));
            }
        }
        $manager->flush();


    }


}
