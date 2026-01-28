<?php

namespace App\Services\Game\Infrastructure;

use App\Services\Game\Domain\Game;
use App\Services\Game\Domain\GameInvitation;
use App\Services\User\Domain\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<GameInvitation>
 */
class GameInvitationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameInvitation::class);
    }

    /**
     * @param Game $game
     * @return GameInvitation[]
     */
    public function getOutstandingInvitationsForGame(Game $game) : iterable {

        $qb = $this->createQueryBuilder('gi');

        $qb->innerJoin('gi.game', 'gig')
            ->where($qb->expr()->eq('gig.id', ':gameId'))
            ->andWhere($qb->expr()->gt('gi.expiresAt', ':now'))
            ->andWhere($qb->expr()->isNull('gi.dateUsed'));
        $qb->setParameter('now', new \DateTimeImmutable());
        $qb->setParameter(':gameId', $game->getId());

        $results = $qb->getQuery()->getArrayResult();
        return new ArrayCollection(self::mapArrayResults($results));
    }

    public function findByInvitationCode(string $invitationCode): ? GameInvitation {
        $qb = $this->createQueryBuilder('gi');

        $qb ->where($qb->expr()->eq('gi.invitationCode', ':invitationCode'))
            ->andWhere($qb->expr()->gt('gi.expiresAt', ':now'))
            ->andWhere($qb->expr()->isNull('gi.dateUsed'));
        $qb->setParameter('now', new \DateTimeImmutable());
        $qb->setParameter(':invitationCode', $invitationCode);

        $result = $qb->getQuery()->getOneOrNullResult();
        return $result instanceof GameInvitation ? $result : null;
    }

    public function findByInvitationCodeAndEmailAddress(string $invitationCode, string $emailAddress): ? GameInvitation {
        $qb = $this->createQueryBuilder('gi');

        $qb ->where($qb->expr()->eq('gi.invitationCode', ':invitationCode'))
            ->andWhere($qb->expr()->gt('gi.expiresAt', ':now'))
            ->andWhere($qb->expr()->isNull('gi.dateUsed'))
            ->andWhere($qb->expr()->eq('gi.email', ':emailAddress'));

        $qb->setParameter('now', new \DateTimeImmutable());
        $qb->setParameter(':invitationCode', $invitationCode);
        $qb->setParameter(':emailAddress', $emailAddress);

        $result = $qb->getQuery()->getResult();
        return $result instanceof GameInvitation ? $result : null;
    }

    /**
     * @return GameInvitation[]
     */
    public function getExpiredInvitations(): array {
        $qb = $this->createQueryBuilder('gi');
        $qb->where($qb->expr()->lte('gi.expiresAt', ':now'));
        $qb->setParameter(':now', new \DateTimeImmutable());
        return self::mapArrayResults($qb->getQuery()->getArrayResult());
    }


    /**
     * @param User $user
     * @return array<GameInvitation>
     */
    public function getOutstandingInvitationsForUser(User $user): array {
        $qb = $this->createQueryBuilder('gi');
            $qb->where($qb->expr()->gt('gi.expiresAt', ':now'))
            ->andWhere($qb->expr()->isNull('gi.dateUsed'))
            ->andWhere($qb->expr()->eq('gi.email', ':emailAddress'));

        $qb->setParameter('now', new \DateTimeImmutable());
        $qb->setParameter(':emailAddress', $user->getEmail());
        return $qb->getQuery()->getResult();
    }

    /**
     * @param string $emailAddress
     * @return array<GameInvitation>
     */
    public function findInvitationsByEmailAddress(string $emailAddress): array {
        $qb = $this->createQueryBuilder('gi');

        $qb->where($qb->expr()->eq('gi.email', ':emailAddress'));
        $qb->setParameter(':emailAddress', $emailAddress);
        return self::mapArrayResults($qb->getQuery()->getArrayResult());
    }

    /**
     * @param array<mixed> $results
     * @return array<GameInvitation>
     */
    public static function mapArrayResults(array $results): array {
        $mappedResults = [];
        foreach ($results as $result) {
            if ($result instanceof GameInvitation) {
                $mappedResults[] = $result;
            }
        }
        return $mappedResults;
    }
}
