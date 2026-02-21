<?php

declare(strict_types=1);
namespace App\Message;

use App\Services\Payment\Domain\Payment;
use App\Services\Payment\Domain\ValueObjects\PaymentStatus;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage]
readonly class OrderStatusUpdateMessage
{
    public function __construct(
        private string $orderReference,
        private string $oldStatus,
        private string $newStatus,

    ) {}

    /**
     * @throws \JsonException
     */
    public function getContent(): string {
        $jsonArray = [
            'orderReference' => $this->orderReference,
            'oldStatus' => $this->oldStatus,
            'newStatus' => $this->newStatus,
        ];
        return json_encode($jsonArray, JSON_THROW_ON_ERROR);
    }

}
