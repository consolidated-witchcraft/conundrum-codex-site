<?php

declare(strict_types=1);

namespace App\Services\FeatureSwitcher\Service;

use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\FeatureSwitcher\Domain\FeatureSwitcher;
use App\Services\FeatureSwitcher\Infrastructure\FeatureSwitcherRepository;
use App\Services\FeatureSwitcher\Service\Exceptions\UnknownFeatureHandleException;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class FeatureSwitcherService extends AbstractDomainEntityService implements FeatureSwitcherServiceInterface
{

    private const string CACHE_KEY_PATTERN = 'FEATURE_SWITCHER_%s';

    public function __construct(
        private readonly FeatureSwitcherRepository $featureSwitcherRepository,
        private readonly CacheInterface $cache,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
    }

    /**
     * @return Collection<FeatureSwitcher>
     */
    public function getAllFeatureSwitchers(): Collection
    {
        return $this->featureSwitcherRepository->findAllFeatureSwitchers();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getByHandle(string $featureHandle, bool $skipCache = false): ?FeatureSwitcher
    {
        $cacheKey = self::getCacheKeyForHandle($featureHandle);

        if ($skipCache) {
            $this->cache->delete($cacheKey);
        }

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($featureHandle) {
            return $this->featureSwitcherRepository->findByHandle($featureHandle);
        });
    }

    /**
     * @param string $featureHandle
     * @return bool
     * @throws UnknownFeatureHandleException
     */
    public function isFeatureEnabled(string $featureHandle): bool
    {
        $featureSwitcher = $this->getByHandle($featureHandle);
        if ($featureSwitcher === null) {
            throw new UnknownFeatureHandleException($featureHandle);
        }
        return $featureSwitcher->isEnabled();
    }

    public function save(FeatureSwitcher $featureSwitcher, bool $flush = true): FeatureSwitcher
    {
        $this->entityManager->persist($featureSwitcher);
        $this->cache->delete(self::getCacheKeyForHandle($featureSwitcher->getHandle()));
        if ($flush) {
            $this->flush();
        }
        return $featureSwitcher;
    }

    public static function getCacheKeyForHandle(string $featureHandle): string {
        return sprintf(self::CACHE_KEY_PATTERN, $featureHandle);
    }
}
