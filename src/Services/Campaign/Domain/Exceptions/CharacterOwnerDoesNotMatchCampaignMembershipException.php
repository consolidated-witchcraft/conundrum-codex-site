<?php

declare(strict_types=1);

namespace App\Services\Campaign\Domain\Exceptions;

class CharacterOwnerDoesNotMatchCampaignMembershipException extends AbstractCampaignDomainException
{
    private const string MESSAGE_PATTERN = 'A campaign character must belong to the user represented by its campaign membership.';

    public function __construct(int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE_PATTERN, $code, $previous);
    }
}
