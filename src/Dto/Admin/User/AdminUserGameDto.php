<?php

declare(strict_types=1);

namespace App\Dto\Admin\User;

use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;

class AdminUserGameDto
{
    public function __construct(
        public readonly User $user,
        public array $games = []
    ) {}

    public static function makeFromUser(User $user): AdminUserGameDto {

        return new static($user);
    }
}
