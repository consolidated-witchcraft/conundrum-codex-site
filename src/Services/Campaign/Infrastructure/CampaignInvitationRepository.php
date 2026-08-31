<?php

declare(strict_types=1);

namespace App\Services\Campaign\Infrastructure;

use App\Services\Campaign\Domain\Campaign;
use App\Services\Campaign\Domain\CampaignInvitation;
use App\Services\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<CampaignInvitation> */
class CampaignInvitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(registry: $registry, entityClass: CampaignInvitation::class);
    }

    /** @return list<CampaignInvitation> */
    public function findOutstandingForCampaign(Campaign $campaign): array
    {
        return $this->createOutstandingQueryBuilder()
            ->andWhere('invitation.campaign = :campaign')
            ->setParameter(key: 'campaign', value: $campaign)
            ->getQuery()->getResult();
    }

    /** @return list<CampaignInvitation> */
    public function findOutstandingForUser(User $user): array
    {
        return $this->createOutstandingQueryBuilder()
            ->andWhere('invitation.email = :email')
            ->setParameter(key: 'email', value: $user->getEmail())
            ->getQuery()->getResult();
    }

    public function findOutstandingByCode(string $code): ?CampaignInvitation
    {
        $result = $this->createOutstandingQueryBuilder()
            ->andWhere('invitation.invitationCode = :code')
            ->setParameter(key: 'code', value: $code)
            ->getQuery()->getOneOrNullResult();

        return $result instanceof CampaignInvitation ? $result : null;
    }

    public function findOutstandingByCodeAndEmail(string $code, string $email): ?CampaignInvitation
    {
        $result = $this->createOutstandingQueryBuilder()
            ->andWhere('invitation.invitationCode = :code')
            ->andWhere('invitation.email = :email')
            ->setParameter(key: 'code', value: $code)
            ->setParameter(key: 'email', value: $email)
            ->getQuery()->getOneOrNullResult();

        return $result instanceof CampaignInvitation ? $result : null;
    }

    /** @return list<CampaignInvitation> */
    public function findByEmail(string $email): array
    {
        return $this->findBy(criteria: ['email' => $email]);
    }

    /** @return list<CampaignInvitation> */
    public function findExpired(): array
    {
        return $this->createQueryBuilder(alias: 'invitation')
            ->where('invitation.expiresAt <= :now')
            ->setParameter(key: 'now', value: new \DateTimeImmutable())
            ->getQuery()->getResult();
    }

    private function createOutstandingQueryBuilder(): \Doctrine\ORM\QueryBuilder
    {
        return $this->createQueryBuilder(alias: 'invitation')
            ->where('invitation.expiresAt > :now')
            ->andWhere('invitation.usedAt IS NULL')
            ->setParameter(key: 'now', value: new \DateTimeImmutable());
    }
}
