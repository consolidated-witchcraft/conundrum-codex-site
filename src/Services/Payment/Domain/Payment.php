<?php

declare(strict_types=1);

namespace App\Services\Payment\Domain;

use App\Services\Orders\Domain\Exceptions\OrderPaymentInvalidStatusChangeException;
use App\Services\Orders\Domain\Exceptions\UnknownPaymentStatusException;
use App\Services\Orders\Domain\Order;
use App\Services\Payment\Domain\ValueObjects\PaymentStatus;
use App\Services\Payment\Infrastructure\PaymentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    /**
     * @throws UnknownPaymentStatusException
     */
    public function __construct(
        #[ORM\Column(type: 'string', length: 255)]
        private readonly string $name,

        #[ORM\Column(type: 'integer')]
        private readonly int $paymentAmountCentessimal,

        #[ORM\Column(type: 'string', length: 255)]
        private readonly string $humanReadablePaymentReference,

        #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderPayments')]
        private readonly Order  $order,

        #[ORM\Column(type: 'payment_status')]
        private ? PaymentStatus $paymentStatus = null,

        #[ORM\Id]
        #[ORM\Column(type: UuidType::NAME, unique: true)]
        #[ORM\GeneratedValue(strategy: 'CUSTOM')]
        #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
        private ? Uuid $paymentReference = null
    ) {
        if ($this->paymentStatus === null) {
            $this->paymentStatus = PaymentStatus::makeFromHandle('unpaid');
        }
    }

    public function getHumanReadablePaymentReference(): string
    {
        return $this->humanReadablePaymentReference;
    }

    public function getPaymentReference(): Uuid
    {
        return $this->paymentReference;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPaymentAmountCentessimal(): int {
        return $this->paymentAmountCentessimal;
    }

    public function getPaymentAmountDecimal(): float {
        return $this->paymentAmountCentessimal/100;
    }

    public function getPaymentStatus(): PaymentStatus {
        return $this->paymentStatus;
    }

    /**
     * @throws UnknownPaymentStatusException
     * @throws OrderPaymentInvalidStatusChangeException
     */
    public function markProcessing(): void {
        if ($this->paymentStatus->getHandle() !== 'unpaid') {
            throw new OrderPaymentInvalidStatusChangeException(
                orderId: $this->order->getId(),
                paymentReference: $this->getPaymentReference()->toString(),
                currentStatusHandle: $this->paymentStatus->getHandle(),
                attemptedStatusHandle: 'processing'
            );
        }
        $this->paymentStatus = PaymentStatus::makeFromHandle('processing');
    }

    public function markDeclined(): void {
        if ($this->paymentStatus->getHandle() !== 'processing') {
            throw new OrderPaymentInvalidStatusChangeException(
                orderId: $this->order->getId(),
                paymentReference: $this->getPaymentReference()->toString(),
                currentStatusHandle: $this->paymentStatus->getHandle(),
                attemptedStatusHandle: 'declined'
            );
        }
        $this->paymentStatus = PaymentStatus::makeFromHandle('declined');
    }

    public function markPaid(): void {
        if ($this->paymentStatus->getHandle() !== 'processing') {
            throw new OrderPaymentInvalidStatusChangeException(
                orderId: $this->order->getId(),
                paymentReference: $this->getPaymentReference()->toString(),
                currentStatusHandle: $this->paymentStatus->getHandle(),
                attemptedStatusHandle: 'paid'
            );
        }
        $this->paymentStatus = PaymentStatus::makeFromHandle('paid');
    }

    public function getOrder(): Order {
        return $this->order;
    }

}
