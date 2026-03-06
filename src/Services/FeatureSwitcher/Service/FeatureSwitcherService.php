<?php

declare(strict_types=1);

namespace App\Services\FeatureSwitcher\Service;

use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\FeatureSwitcher\Domain\FeatureSwitcher;
use App\Services\FeatureSwitcher\Infrastructure\FeatureSwitcherRepository;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class FeatureSwitcherService extends AbstractDomainEntityService implements FeatureSwitcherServiceInterface
{
    public function __construct(
        private readonly FeatureSwitcherRepository $featureSwitcherRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
    }

    /**
     * @return Collection<FeatureSwitcher>
     */
    public function getAllFeatureSwitchers(): Collection
    {
        return $this->featureSwitcherRepository->findAll();
    }

    public function getByHandle(string $featureHandle): ?FeatureSwitcher
    {
        return $this->featureSwitcherRepository->findByHandle($featureHandle);
    }

    public function isFeatureEnabled(string $featureHandle): bool
    {
        $featureSwitcher = $this->getByHandle($featureHandle);
        if ($featureSwitcher === null) {
            return false;
        }
        return $featureSwitcher->isEnabled();
    }

    public function save(FeatureSwitcher $featureSwitcher, bool $flush = true): FeatureSwitcher
    {
        $this->entityManager->persist($featureSwitcher);
        if ($flush) {
            $this->flush();
        }
    }
}
