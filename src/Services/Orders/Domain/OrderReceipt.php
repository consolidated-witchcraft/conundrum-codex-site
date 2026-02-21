<?php

declare(strict_types=1);

namespace App\Services\Orders\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;

use App\Services\Orders\Infrastructure\OrderReceiptRepository;
use App\Services\Orders\Infrastructure\OrderTaxRepository;
use App\Services\Payment\Domain\Payment;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: OrderReceiptRepository::class)]
#[UniqueEntity(fields: ['receiptReference'])]
class OrderReceipt extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column(type: Types::STRING, nullable: false, unique: true)]
        private readonly string $receiptReference,

        #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderReceipts')]
        private readonly Order $order,

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

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getAmountCentessimal(): int
    {
        return $this->amountCentessimal;
    }

    public function getReceiptDate(): \DateTimeImmutable
    {
        return $this->receiptDate;
    }

    /**
     * @return Collection<int, OrderProductLine>
     */
    public function getProductLines(): Collection {
        return $this->getOrder()->getProductOrderLines();
    }

    /**
     * @return Collection<int, OrderChargeLine>
     */
    public function getChargeLines(): Collection {
        return $this->getOrder()->getChargeLines();
    }

    /**
     * @return Collection<int, Payment>
     */
    public function getOrderPayments(): Collection {
        return $this->getOrder()->getOrderPayments();
    }

    /**
     * @return Collection<int, OrderTax>
     */
    public function getOrderTaxes(): Collection {
        return $this->getOrder()->getOrderTaxes();
    }

}
