<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface;

beforeEach(function () {
    static::bootKernel();
    $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
});

test('an authenticated user can get the puzzle template collection', function () {
    $user = makeUser(entityManager: $this->entityManager);

    static::ensureKernelShutdown();
    $client = $this->makeAuthenticatedClient(user: $user);
    $client->request(
        method: 'GET',
        uri: '/api/puzzles/templates',
        server: ['HTTP_ACCEPT' => 'application/ld+json']
    );

    $this->assertResponseIsSuccessful();

    $payload = json_decode($client->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);
    $items = apiCollectionItems(payload: $payload);
    $templateSlugs = array_column($items, 'templateSlug');

    expect($templateSlugs)->toContain('buttons');
    expect($templateSlugs)->toContain('casebook');
});

test('an authenticated user can get an individual puzzle template', function () {
    $user = makeUser(entityManager: $this->entityManager);

    static::ensureKernelShutdown();
    $client = $this->makeAuthenticatedClient(user: $user);
    $client->request(
        method: 'GET',
        uri: '/api/puzzles/templates/buttons',
        server: ['HTTP_ACCEPT' => 'application/ld+json']
    );

    $this->assertResponseIsSuccessful();

    $payload = json_decode($client->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);

    expect($payload['templateSlug'])->toBe('buttons');
});
