<?php

declare(strict_types=1);

namespace App\Services\PlayEvent\Domain\Enums;

enum PlayEventTypeEnum: string
{
    case INSTANCE_START = 'instance_start';
    case INSTANCE_WIN = 'instance_win';
    case INSTANCE_FAIL = 'instance_fail';
}
