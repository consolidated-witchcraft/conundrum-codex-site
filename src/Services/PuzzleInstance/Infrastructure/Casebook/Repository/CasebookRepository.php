<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Infrastructure\Casebook\Repository;

use App\Services\Game\Domain\Game;
use App\Services\PuzzleInstance\Domain\Casebook\Casebook;
use App\Services\PuzzleInstance\Domain\Interfaces\PuzzleInstanceProviderInterface;
use App\Services\PuzzleInstance\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\PuzzleInstance\Domain\Interfaces\StaticPuzzleInstanceProviderInterface;
use App\Services\PuzzleInstance\Infrastructure\CodeGenerator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use Random\RandomException;

/**
 * @extends ServiceEntityRepository<Casebook>
 */
class CasebookRepository extends ServiceEntityRepository implements StaticPuzzleInstanceProviderInterface, PuzzleInstanceProviderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Casebook::class);
    }


    public function findBySlug(string $slug): ? Casebook {
        $result = $this->createQueryBuilder('p')
            ->where('p.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult();
        return $result instanceof Casebook ? $result : null;
    }

    public function decollideInstanceCode(string $candidateInstanceCode, int $attempts = 0): string {

        if (null === $this->findBySlug($candidateInstanceCode)) {
            return $candidateInstanceCode;
        }

        $attempts++;
        $newCandidateInstanceCode = sprintf("%s_%d", $candidateInstanceCode, $attempts);
        if (null === $this->findBySlug($newCandidateInstanceCode)) {
            return $newCandidateInstanceCode;
        } else {
            return $this->decollideInstanceCode($candidateInstanceCode, $attempts);
        }
    }

    /**
     * @return Collection<int, Casebook>
     */
    public function getStaticPuzzleInstancesForGame(Game $game): Collection
    {
        $qb = $this->createQueryBuilder('cp')
            ->where('g = :game')
            ->innerJoin('cp.game', 'g')
            ->setParameter(':game', $game);

        return new ArrayCollection($qb->getQuery()->getResult());
    }

    public function providesTemplateInstances(): string
    {
        return Casebook::TEMPLATE_SLUG;
    }

    public function getInstance(string $instanceCode): ?PuzzleInstanceInterface
    {
        return $this->findBySlug($instanceCode);
    }

    /**
     * @throws RandomException
     */
    public function generateNewInstanceCode(?string $candidateInstanceCode = null, int $attempts = 0): string
    {
        if ($candidateInstanceCode === null) {
            $candidateInstanceCode = CodeGenerator::generateRandomCode();
        }
        return $this->decollideInstanceCode($candidateInstanceCode, $attempts);
    }
}
