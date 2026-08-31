<?php

declare(strict_types=1);

namespace App\Security;

use App\Services\Orders\Domain\Order;
use App\Services\User\Domain\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @template-extends Voter<string, Order>
 */
class UserOwnsOrderVoter extends Voter
{

    public const string OPERATION = 'view_order';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if ($attribute != self::OPERATION) {
            return false;
        }

        if (!$subject instanceof Order) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User && $subject instanceof Order) {
            return false;
        }

        return $user->getUserIdentifier() === $subject->getUser()->getUserIdentifier();

    }
}
