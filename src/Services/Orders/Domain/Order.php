<?php

declare(strict_types=1);

namespace App\Services\Orders\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;

use App\Services\Orders\Infrastructure\OrderRepository;
use App\Services\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
#[UniqueEntity(fields: ['orderReference'], message: 'Order with that reference already exists.')]
class Order extends AbstractDomainEntity
{
    /**
     * @param string $customerEmail
     * @param string $customerName
     * @param Collection<int, ProductOrderLine> $productOrderLines
     * @param Collection<int, OrderChargeLine> $chargeLines
     * @param Collection<int, OrderPayment> $orderPayments
     * @param User|null $user
     * @param int|null $id
     */
    public function __construct(

        #[ORM\Column(type: 'string', unique: true)]
        private string $orderReference,

        #[ORM\OneToMany(targetEntity: ProductOrderLine::class, mappedBy: 'order', cascade: ['remove'], fetch: 'EAGER', orphanRemoval: true)]
        private Collection $productOrderLines,

        #[ORM\OneToMany(targetEntity: OrderChargeLine::class, mappedBy: 'order', cascade: ['remove'], fetch: 'EAGER', orphanRemoval: true)]
        private Collection $chargeLines,

        #[ORM\OneToMany(targetEntity: OrderPayment::class, mappedBy: 'order', fetch: 'EAGER')]
        private readonly Collection $orderPayments,

        #[ORM\ManyToOne(targetEntity: User::class)]
        private User $user,

        ?int $id
    ) {
        parent::__construct($id);
    }

    public function getOrderPayments(): Collection
    {
        return $this->orderPayments;
    }
    public function getCustomerEmail(): string {
        return $this->customerEmail;
    }
    public function getCustomerName(): string {
        return $this->customerName;
    }

    public function setCustomerEmail(string $customerEmail): void {
        $this->customerEmail = $customerEmail;
    }

    public function setCustomerName(string $customerName): void {
        $this->customerName = $customerName;
    }

    public function getProductOrderLines(): Collection {
        return $this->productOrderLines;
    }

    public function setProductOrderLines(Collection $productOrderLines): void {
        $this->productOrderLines = $productOrderLines;
    }

    public function getChargeLines(): Collection {
        return $this->chargeLines;
    }

    public function setChargeLines(Collection $chargeLines): void {
        $this->chargeLines = $chargeLines;
    }

    public function setUser(User $user): void {
        $this->user = $user;
    }

    public function getUser(): ?User {
        return $this->user;
    }
}
