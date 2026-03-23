<?php

declare(strict_types=1);

namespace App\Services\ManagedImages\Domain\Enums;

enum ManagedImageTypeEnum: string
{
    case DEFAULT = 'default';
    case THUMBNAIL = 'thumbnail';
    case AVATAR_THUMBNAIL = 'avatar-thumbnail';
}
