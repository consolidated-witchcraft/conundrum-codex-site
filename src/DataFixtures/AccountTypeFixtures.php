<?php

namespace App\DataFixtures;

use App\Services\User\Domain\Permission;
use App\Services\User\Domain\Role;
use App\Services\User\Domain\ValueObjects\UserAccountType;
use App\Services\User\Infrastructure\Repository\PermissionRepository;
use App\Services\User\Infrastructure\Repository\RoleRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use InvalidArgumentException;

class AccountTypeFixtures extends Fixture
{
    private const array ACCOUNT_TYPES = [
        'free' => [
            'label' => 'Free User',
            'description' => 'Free account.',
        ],
        'pro' => [
            'label' => 'Pro User',
            'description' => 'Pro account.',
        ]


    ];

    public function load(ObjectManager $manager): void
    {

        $accountTypeRepository = $manager->getRepository(UserAccountType::class);
        foreach (self::ACCOUNT_TYPES as $handle => $accountTypeData) {
            if (!$accountTypeRepository->findByHandle($handle)) {
                $manager->persist(new UserAccountType(
                    label: $accountTypeData['label'],
                    handle: $handle,
                    description: $accountTypeData['description'],
                ));
            }
        }
        $manager->flush();

    }
}
