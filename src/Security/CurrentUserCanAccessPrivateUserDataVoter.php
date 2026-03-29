<?php

declare(strict_types=1);

namespace App\Security;

use App\ApiResource\User\Dto\ApiUserDto;
use App\ApiResource\User\Dto\ApiUserPlayEventOutputDto;
use App\Services\PlayEvent\Domain\PlayEvent;
use App\Services\User\Domain\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @template-extends Voter<string, ApiUserDto|ApiUserPlayEventOutputDto|PlayEvent|User>
 */
class CurrentUserCanAccessPrivateUserDataVoter extends Voter
{
    public const string REQUESTED_PERMISSION = 'current_user_can_access_private_user_data';
    private const string PRIVATE_DATA_PERMISSION = 'access_private_data';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute !== self::REQUESTED_PERMISSION) {
            return false;
        }

        return $subject instanceof ApiUserDto
            || $subject instanceof ApiUserPlayEventOutputDto
            || $subject instanceof PlayEvent
            || $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $currentUser = $token->getUser();
        if (!$currentUser instanceof User) {
            return false;
        }

        if ($currentUser->hasPermissionKey(self::PRIVATE_DATA_PERMISSION)) {
            return true;
        }

        return $currentUser->getId() === $this->getSubjectUserId(subject: $subject);
    }

    private function getSubjectUserId(mixed $subject): int
    {
        return match (true) {
            $subject instanceof User => $subject->getId(),
            $subject instanceof ApiUserDto => $subject->getId(),
            $subject instanceof PlayEvent => $subject->getUser()->getId(),
            $subject instanceof ApiUserPlayEventOutputDto => $subject->getUserId(),
            default => 0,
        };
    }
}
