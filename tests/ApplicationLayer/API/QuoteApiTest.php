<?php

declare(strict_types=1);

use Doctrine\ORM\EntityManagerInterface;

beforeEach(function () {
    static::bootKernel();
    $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
});

test('the quote endpoint returns a quotation', function () {
    makeQuotation(entityManager: $this->entityManager);

    static::ensureKernelShutdown();
    $client = $this->makeClient();
    $client->request(
        method: 'GET',
        uri: '/api/quote',
        server: ['HTTP_ACCEPT' => 'application/ld+json']
    );

    $this->assertResponseIsSuccessful();

    $payload = json_decode($client->getResponse()->getContent(), true, flags: JSON_THROW_ON_ERROR);

    expect($payload['quote'])->not->toBe('');
    expect($payload['citation'])->not->toBe('');
});
