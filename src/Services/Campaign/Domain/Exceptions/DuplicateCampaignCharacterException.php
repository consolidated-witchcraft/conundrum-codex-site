<?php

declare(strict_types=1);

namespace App\Services\Campaign\Domain\Exceptions;

class DuplicateCampaignCharacterException extends AbstractCampaignDomainException
{
    private const string MESSAGE_PATTERN = 'This character already belongs to the campaign membership.';

    public function __construct(int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE_PATTERN, $code, $previous);
    }
}
