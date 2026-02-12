<?php

declare(strict_types=1);

namespace App\Services\Orders\Domain;

use App\Services\Files\Domain\ManagedFile;
use App\Services\Orders\Domain\Exceptions\OrderPaymentInvalidStatusChangeException;
use App\Services\Orders\Domain\Exceptions\UnknownPaymentStatusException;
use App\Services\Orders\Domain\ValueObjects\PaymentStatus;
use App\Services\Orders\Infrastructure\OrderPaymentRepository;
use App\Services\Orders\Infrastructure\ProductRepository;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Services\Core\Domain\AbstractDomainEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: OrderPaymentRepository::class)]
class OrderPayment extends AbstractDomainEntity
{
    /**
     * @throws UnknownPaymentStatusException
     */
    public function __construct(
        #[ORM\Column(type: 'string', length: 255)]
        private readonly string $reference,

        #[ORM\Column(type: 'integer')]
        private readonly int $paymentAmountCentessimal,

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orderPayments')]
        private readonly Order  $order,

        #[ORM\Column(type: 'payment_status')]
        private ? PaymentStatus $paymentStatus = null,



        ? int $id = null
    ) {
        parent::__construct($id);
        if ($this->paymentStatus === null) {
            $this->paymentStatus = PaymentStatus::makeFromHandle('unpaid');
        }
    }

    public function getReference(): string {
        return $this->reference;
    }

    public function getPaymentAmountCentessimal(): int {
        return $this->paymentAmountCentessimal;
    }

    public function getPaymentStatus(): PaymentStatus {
        return $this->paymentStatus;
    }

    /**
     * @throws UnknownPaymentStatusException
     * @throws OrderPaymentInvalidStatusChangeException
     */
    public function markProcessing(): void {
        if (!$this->paymentStatus->getHandle() === 'unpaid') {
            throw new OrderPaymentInvalidStatusChangeException(
                orderId: $this->order->getId(),
                paymentId: $this->getId(),
                currentStatusHandle: $this->paymentStatus->getHandle(),
                attemptedStatusHandle: 'processing'
            );
        }
        $this->paymentStatus = PaymentStatus::makeFromHandle('processing');
    }

    public function markDeclined(): void {
        if (!$this->paymentStatus->getHandle() === 'processing') {
            throw new OrderPaymentInvalidStatusChangeException(
                orderId: $this->order->getId(),
                paymentId: $this->getId(),
                currentStatusHandle: $this->paymentStatus->getHandle(),
                attemptedStatusHandle: 'declined'
            );
        }
        $this->paymentStatus = PaymentStatus::makeFromHandle('declined');
    }

    public function markPaid(): void {
        if (!$this->paymentStatus->getHandle() === 'processing') {
            throw new OrderPaymentInvalidStatusChangeException(
                orderId: $this->order->getId(),
                paymentId: $this->getId(),
                currentStatusHandle: $this->paymentStatus->getHandle(),
                attemptedStatusHandle: 'paid'
            );
        }
        $this->paymentStatus = PaymentStatus::makeFromHandle('paid');
    }


}
