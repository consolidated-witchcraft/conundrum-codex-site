<?php

declare(strict_types=1);

namespace App\Services\Campaign\Domain\Enums;

enum CampaignStatusEnum: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case ARCHIVED = 'archived';
}
