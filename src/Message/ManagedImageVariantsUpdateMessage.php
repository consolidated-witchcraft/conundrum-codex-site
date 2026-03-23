<?php

declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage]
readonly class ManagedImageVariantsUpdateMessage
{
    public function __construct(
        private int $managedImageId,
    ) {
    }

    public function getManagedImageId(): int
    {
        return $this->managedImageId;
    }
}
