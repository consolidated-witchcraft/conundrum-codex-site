<?php

declare(strict_types=1);

namespace Tests\ApplicationLayer\Controller\Visitor\Campaigns;

use App\Services\Campaign\Domain\Campaign;
use App\Services\Campaign\Domain\CampaignMembership;
use App\Services\Campaign\Domain\Enums\CampaignMembershipTypeEnum;
use App\Services\Campaign\Service\Interfaces\CampaignServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Tests\ApplicationLayer\API\ApiTestCase;

final class CampaignInvitationControllerTest extends ApiTestCase
{
    public function testGamesMasterCanViewCampaignInvitationForm(): void
    {
        static::bootKernel();
        $container = static::getContainer();
        $entityManager = $container->get(EntityManagerInterface::class);
        makeFeatureSwitchers(entityManager: $entityManager, cache: $container->get(CacheInterface::class));
        $gamesMaster = makeUser(entityManager: $entityManager);
        $campaign = new Campaign(name: 'Lost Observatory', slug: 'lost-observatory', description: 'Among the stars.');
        $campaign->addMembership(membership: new CampaignMembership(
            campaign: $campaign,
            user: $gamesMaster,
            type: CampaignMembershipTypeEnum::GAME_MASTER,
        ));
        static::ensureKernelShutdown();

        $client = $this->makeAuthenticatedClient(user: $gamesMaster);
        $campaignService = $this->createMock(CampaignServiceInterface::class);
        $campaignService->method('findBySlug')->with('lost-observatory')->willReturn($campaign);
        static::getContainer()->set(CampaignServiceInterface::class, $campaignService);
        $crawler = $client->request(method: 'GET', uri: '/campaigns/lost-observatory/invitations/new');

        self::assertResponseIsSuccessful();
        self::assertCount(expectedCount: 1, haystack: $crawler->filter('#invite_player_email'));
        self::assertCount(expectedCount: 1, haystack: $crawler->filter('#invite_player_invitationMessage'));
        self::assertCount(expectedCount: 1, haystack: $crawler->filter('#invite_player_submit'));
    }
}
