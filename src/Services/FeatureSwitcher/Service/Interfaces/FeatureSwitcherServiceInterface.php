<?php

declare(strict_types=1);

namespace App\Services\FeatureSwitcher\Service\Interfaces;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\FeatureSwitcher\Domain\FeatureSwitcher;
use App\Services\FeatureSwitcher\Service\Exceptions\UnknownFeatureHandleException;
use Doctrine\Common\Collections\Collection;

interface FeatureSwitcherServiceInterface extends DomainEntityServiceInterface
{
    /**
     * @return Collection<FeatureSwitcher>
     */
    public function getAllFeatureSwitchers(): Collection;

    public function getByHandle(string $featureHandle, bool $skipCache = false): ? FeatureSwitcher;

    /**
     * @param string $featureHandle
     * @throws UnknownFeatureHandleException
     * @return bool
     */
    public function isFeatureEnabled(string $featureHandle): bool;

    public function save(FeatureSwitcher $featureSwitcher, bool $flush = true): FeatureSwitcher;
}
