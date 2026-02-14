<?php

declare(strict_types=1);
namespace App\Message;

use App\Services\Payment\Domain\Payment;
use App\Services\Payment\Domain\ValueObjects\PaymentStatus;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage]
readonly class PaymentStatusUpdateMessage
{
    public function __construct(
        private string $paymentReference,
        private string $orderReference,
        private int $amountCentessimal,
        private string $paymentName,
        private PaymentStatus $oldPaymentStatus,
        private PaymentStatus $newPaymentStatus
    ) {}

    /**
     * @throws \JsonException
     */
    public function getContent(): string {
        $jsonArray = [
            'paymentReference' => $this->paymentReference,
            'oldPaymentStatus' => $this->oldPaymentStatus->getHandle(),
            'newPaymentStatus' => $this->newPaymentStatus->getHandle(),
            'name' => $this->paymentName,
            'amountCentessimal' => $this->amountCentessimal,
            'orderReference' => $this->orderReference,
        ];
        return json_encode($jsonArray, JSON_THROW_ON_ERROR);
    }

}
