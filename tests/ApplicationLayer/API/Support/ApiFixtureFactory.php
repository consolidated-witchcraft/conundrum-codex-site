<?php

declare(strict_types=1);

use App\Services\Quotation\Domain\Quotation;
use App\Services\FeatureSwitcher\Domain\FeatureSwitcher;
use App\Services\User\Domain\Role;
use App\Services\User\Domain\User;
use App\Services\User\Domain\ValueObjects\UserAccountType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;

function makeUser(EntityManagerInterface $entityManager): User
{
    $suffix = bin2hex(random_bytes(length: 8));
    $role = $entityManager->getRepository(Role::class)->findOneBy(criteria: ['handle' => 'ROLE_USER']);

    if (!$role instanceof Role) {
        $role = new Role(
            label: 'User',
            handle: 'ROLE_USER',
        );
        $entityManager->persist(object: $role);
    }

    $accountType = new UserAccountType(
        label: 'API Test Account',
        handle: 'api_test_'.$suffix,
        description: 'Account type created by an API test.',
    );
    $user = new User(
        email: 'api-test-'.$suffix.'@example.com',
        username: 'API Test '.$suffix,
        userAccountType: $accountType,
        password: 'not-a-real-password',
    );
    $user->addRole(role: $role);

    $entityManager->persist(object: $accountType);
    $entityManager->persist(object: $user);
    $entityManager->flush();

    return $user;
}

function makeFeatureSwitchers(EntityManagerInterface $entityManager, CacheInterface $cache): void
{
    $repository = $entityManager->getRepository(FeatureSwitcher::class);

    foreach (['EVENTS_SYSTEM' => 'Events System', 'WORLDS_SYSTEM' => 'Worlds System'] as $handle => $label) {
        if (!$repository->findOneBy(criteria: ['handle' => $handle]) instanceof FeatureSwitcher) {
            $entityManager->persist(object: new FeatureSwitcher(handle: $handle, label: $label));
        }

        $cache->delete(key: 'FEATURE_SWITCHER_'.$handle);
    }

    $entityManager->flush();
}

function makeQuotation(EntityManagerInterface $entityManager): Quotation
{
    $quotation = new Quotation(
        quotation: 'The answer is hidden in the question.',
        citation: 'API test fixture',
    );

    $entityManager->persist(object: $quotation);
    $entityManager->flush();

    return $quotation;
}

/**
 * @param array<string, mixed> $payload
 * @return array<int, array<string, mixed>>
 */
function apiCollectionItems(array $payload): array
{
    return $payload['member'] ?? $payload['hydra:member'] ?? $payload;
}
