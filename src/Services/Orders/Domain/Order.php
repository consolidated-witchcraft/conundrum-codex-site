<?php

declare(strict_types=1);

namespace App\Services\Orders\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Core\Domain\Interfaces\DomainEntityInterface;
use App\Services\Orders\Infrastructure\OrderRepository;
use App\Services\Payment\Domain\Payment;
use App\Services\Payment\Domain\ValueObjects\PaymentStatus;
use App\Services\PostalAddress\Domain\PostalAddress;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
class Order extends AbstractDomainEntity implements DomainEntityInterface
{
    private const string ORDER_REFERENCE_PATTERN = "ORD-%d_%s";

    /**
     * @param Collection<int, OrderProductLine> $productOrderLines
     * @param Collection<int, OrderChargeLine> $chargeLines
     * @param Collection<int, Payment> $orderPayments
     * @param User $user
     * @param Collection<int, OrderTax> $orderTaxes
     * @param int|null $id
     */
    public function __construct(
        #[ORM\OneToMany(targetEntity: OrderProductLine::class, mappedBy: 'order', cascade: ['persist', 'refresh', 'remove'], fetch: 'EAGER', orphanRemoval: true)]
        private Collection $productOrderLines,

        #[ORM\OneToMany(targetEntity: OrderChargeLine::class, mappedBy: 'order', cascade: ['persist', 'refresh', 'remove'], fetch: 'EAGER', orphanRemoval: true)]
        private Collection $chargeLines,

        #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'order', cascade: ['persist', 'refresh', 'remove'], fetch: 'EAGER', orphanRemoval: true)]
        private Collection $orderPayments,

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
        private User $user,

        #[ORM\OneToMany(targetEntity: OrderTax::class, mappedBy: 'order', cascade: ['persist', 'refresh','remove'], orphanRemoval: true)]
        private Collection $orderTaxes,

        #[ORM\ManyToOne(targetEntity: PostalAddress::class)]
        private ? PostalAddress $deliveryAddress = null,

        #[ORM\ManyToOne(targetEntity: PostalAddress::class)]
        private ? PostalAddress $billingAddress = null,


        ?int $id = null

    ) {
        parent::__construct($id);
    }

    public function getDeliveryAddress(): ?PostalAddress
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress(PostalAddress $deliveryAddress): void
    {
        $this->deliveryAddress = $deliveryAddress;
    }

    public function getBillingAddress(): ?PostalAddress
    {
        return $this->billingAddress;
    }

    public function setBillingAddress(PostalAddress $billingAddress): void
    {
        $this->billingAddress = $billingAddress;
    }

    /**
     * @return Collection<int, OrderTax>
     */
    public function getOrderTaxes(): Collection
    {
        return $this->orderTaxes;
    }

    /**
     * @param Collection<int, OrderTax> $orderTaxes
     * @return void
     */
    public function setOrderTaxes(Collection $orderTaxes): void
    {
        $this->orderTaxes = $orderTaxes;
    }

    /**
     * @param Collection<int, Payment> $orderPayments
     * @return void
     */
    public function setOrderPayments(Collection $orderPayments): void {
        $this->orderPayments = $orderPayments;
    }
    /**
     * @return Collection<int, Payment>
     */
    public function getOrderPayments(): Collection
    {
        return $this->orderPayments;
    }

    /**
     * @return Collection<int, OrderProductLine>
     */
    public function getProductOrderLines(): Collection {
        return $this->productOrderLines;
    }

    /**
     * @param Collection<int, OrderProductLine> $productOrderLines
     * @return void
     */
    public function setProductOrderLines(Collection $productOrderLines): void {
        $this->productOrderLines = $productOrderLines;
    }

    /**
     * @return Collection<int, OrderChargeLine>
     */
    public function getChargeLines(): Collection {
        return $this->chargeLines;
    }

    /**
     * @param Collection<int, OrderChargeLine> $chargeLines
     * @return void
     */
    public function setChargeLines(Collection $chargeLines): void {
        $this->chargeLines = $chargeLines;
    }

    public function setUser(User $user): void {
        $this->user = $user;
    }

    public function getUser(): ?User {
        return $this->user;
    }

    public function getTotalPostTaxCentessimal(): int {
        return $this->getProductLineTotalPreTaxCentessimal() + $this->getChargeLineTotalPreTaxCentessimal() + $this->getTotalTaxCentessimal();
    }

    public function getProductLineTotalPreTaxCentessimal(): int {
        $productsPreTaxCentessimal = 0;
        foreach ($this->productOrderLines as $productOrderLine) {
            $productsPreTaxCentessimal+= $productOrderLine->getTotalCentessimal();
        }
        return $productsPreTaxCentessimal;
    }

    public function getChargeLineTotalPreTaxCentessimal(): int {
        $totalPostTaxCentessimal = 0;
        foreach ($this->chargeLines as $chargeLine) {
            $totalPostTaxCentessimal+= $chargeLine->getPriceCentessimal();
        }
        return $totalPostTaxCentessimal;
    }

    public function getTotalTaxCentessimal(): int {
        $totalTaxCentessimal = 0;
        foreach ($this->orderTaxes as $orderTax) {
            $totalTaxCentessimal+= $orderTax->getAmountCentessimal();
        }
        return $totalTaxCentessimal;
    }

    public function getTotalPostTaxDecimal(): float {
        return $this->getTotalPostTaxCentessimal()/100;
    }

    public function getOrderPaymentStatus(): PaymentStatus {
        $totalPaymentsCount = $this->orderPayments->count();

        $paidPaymentsCount = 0;

        foreach ($this->orderPayments as $orderPayment) {
            if ($orderPayment->getPaymentStatus()->getHandle() === 'paid') {
                $paidPaymentsCount++;
            }
        }
        if ($paidPaymentsCount === $totalPaymentsCount) {
            return PaymentStatus::makeFromHandle('paid');
        }

        if ($paidPaymentsCount > 0) {
            return PaymentStatus::makeFromHandle('partially_paid');
        }
        return PaymentStatus::makeFromHandle('unpaid');
    }
}
