<?php

declare(strict_types=1);

namespace App\Services\Campaign\Domain\Exceptions;

class CampaignCharacterDoesNotBelongToMembershipException extends AbstractCampaignDomainException
{
    private const string MESSAGE_PATTERN = 'The campaign character does not belong to this campaign membership.';

    public function __construct(int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE_PATTERN, $code, $previous);
    }
}
