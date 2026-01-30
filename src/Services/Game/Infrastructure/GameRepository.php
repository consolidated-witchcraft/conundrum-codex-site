<?php

declare(strict_types=1);

namespace App\Services\Game\Infrastructure;

use App\Services\Game\Domain\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Game>
 */
class GameRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    public function findOneBySlug(string $slug): ? Game {
        return $this->findOneBy([
            'slug' => $slug
        ]);
    }

    /**
     * @param bool $includeArchived
     * @return Game[]
     */
    public function findAllGames(bool $includeArchived = false): iterable {
        $qb = $this->createQueryBuilder('g');

        if (!$includeArchived) {
            $eb = $qb->expr();
            $qb->select('g')
                ->where($eb->isNull('g.archivedDate'));
        }

        return $qb->getQuery()->getResult();
    }
}
