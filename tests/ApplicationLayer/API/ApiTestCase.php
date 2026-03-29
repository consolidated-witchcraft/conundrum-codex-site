<?php

declare(strict_types=1);

namespace Tests\ApplicationLayer\API;

use App\Services\User\Domain\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class ApiTestCase extends WebTestCase
{
    protected function makeClient(): KernelBrowser
    {
        return static::createClient();
    }

    protected function makeAuthenticatedClient(User $user): KernelBrowser
    {
        $client = $this->makeClient();
        $client->loginUser($user);

        return $client;
    }
}
