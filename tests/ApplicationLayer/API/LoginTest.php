<?php

declare(strict_types=1);

namespace Tests\ApplicationLayer\API;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;

final class LoginTest extends ApiTestCase
{
    public function testInvalidCredentialsRedirectBackToLoginWithoutServerError(): void
    {
        static::bootKernel();
        makeFeatureSwitchers(
            entityManager: static::getContainer()->get(EntityManagerInterface::class),
            cache: static::getContainer()->get(CacheInterface::class),
        );
        static::ensureKernelShutdown();

        $client = $this->makeClient();
        $crawler = $client->request('GET', '/login');

        self::assertResponseIsSuccessful();

        $client->submitForm('Sign in', [
            '_username' => 'missing@example.com',
            '_password' => 'invalid-password',
            '_csrf_token' => $crawler->filter('input[name="_csrf_token"]')->attr('value'),
        ]);

        self::assertResponseRedirects('/login');

        $client->followRedirect();

        self::assertResponseIsSuccessful();
        self::assertStringContainsString('Invalid credentials.', $client->getResponse()->getContent());
    }
}
