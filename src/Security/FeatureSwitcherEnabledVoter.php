<?php

namespace App\Security;

use App\Services\FeatureSwitcher\Domain\FeatureSwitcher;
use App\Services\FeatureSwitcher\Service\Exceptions\UnknownFeatureHandleException;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @template-extends Voter<string, FeatureSwitcher>
 */
class FeatureSwitcherEnabledVoter extends Voter
{
    public function __construct(
        private readonly FeatureSwitcherServiceInterface $featureSwitcherService,
    ) {

    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return (str_starts_with($attribute, 'FS_'));

    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $prefixlessHandle = substr($attribute, 3);

        try {
            $featureSwitcher = $this->featureSwitcherService->getByHandle($prefixlessHandle);
        } catch (UnknownFeatureHandleException $e) {
            return false;
        }

        return $featureSwitcher->isEnabled();
    }
}
