<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface;

beforeEach(function () {
    static::bootKernel();
    $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
});

test('an authenticated user can roll dice', function () {
    $user = makeUser(entityManager: $this->entityManager);

    static::ensureKernelShutdown();
    $client = $this->makeAuthenticatedClient(user: $user);
    $client->request(
        method: 'GET',
        uri: '/api/dice/1d6',
        server: ['HTTP_ACCEPT' => 'application/ld+json']
    );

    $this->assertResponseIsSuccessful();

    $payload = json_decode($client->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
    $items = apiCollectionItems(payload: $payload);

    expect($items)->toHaveCount(1);
    expect($items[0]['id'])->toBe('1d6');
    expect($items[0]['dieSides'])->toBe(6);
});

test('an invalid dice roll returns an unprocessable response', function () {
    $user = makeUser(entityManager: $this->entityManager);

    static::ensureKernelShutdown();
    $client = $this->makeAuthenticatedClient(user: $user);
    $client->request(
        method: 'GET',
        uri: '/api/dice/not-a-roll',
        server: ['HTTP_ACCEPT' => 'application/ld+json']
    );

    $this->assertResponseStatusCodeSame(422);
});
