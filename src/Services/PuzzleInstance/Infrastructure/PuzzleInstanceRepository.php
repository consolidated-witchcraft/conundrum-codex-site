<?php

namespace App\Services\PuzzleInstance\Infrastructure;

use App\Services\PuzzleInstance\Domain\Interfaces\PuzzleInstanceCodeProviderInterface;
use App\Services\PuzzleInstance\Domain\PuzzleInstance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PuzzleInstance>
 */
class PuzzleInstanceRepository extends ServiceEntityRepository implements PuzzleInstanceCodeProviderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PuzzleInstance::class);
    }

    public function findByInstanceCode(string $instanceCode): ? PuzzleInstance {
        return $this->findOneBy(['instanceCode' => $instanceCode]);
    }

    public function decollideInstanceCode(string $candidateInstanceCode, int $attempts = 0): string
    {
        if (null === $this->findByInstanceCode($candidateInstanceCode)) {
            return $candidateInstanceCode;
        }

        $attempts++;
        $newCandidateInstanceCode = sprintf("%s_%d", $candidateInstanceCode, $attempts);
        if (null === $this->findByInstanceCode($newCandidateInstanceCode)) {
            return $newCandidateInstanceCode;
        } else {
            return $this->decollideInstanceCode($candidateInstanceCode, $attempts);
        }
    }
}
