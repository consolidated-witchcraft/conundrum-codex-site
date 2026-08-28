<?php

declare(strict_types=1);

namespace App\Services\Payment\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Payment\Infrastructure\PaymentReceiptRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: PaymentReceiptRepository::class)]
#[UniqueEntity(fields: ['receiptReference'])]
class PaymentReceipt extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column(type: Types::STRING, nullable: false, unique: true)]
        private readonly string $receiptReference,

        #[ORM\ManyToOne(targetEntity: Payment::class, inversedBy: 'paymentReceipts')]
        #[ORM\JoinColumn(nullable: false)]
        private Payment $payment,

        #[ORM\Column(type: Types::INTEGER)]
        private readonly int $amountCentessimal,

        #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
        private readonly \DateTimeImmutable $receiptDate,
        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getReceiptReference(): string
    {
        return $this->receiptReference;
    }

    public function getPayment(): Payment
    {
        return $this->payment;
    }

    public function setPayment(Payment $payment): void {
        $this->payment = $payment;
    }

    public function getAmountCentessimal(): int
    {
        return $this->amountCentessimal;
    }

    public function getAmountDecimal(): float {
        return $this->getAmountCentessimal()/100;
    }

    public function getReceiptDate(): \DateTimeImmutable
    {
        return $this->receiptDate;
    }

}
