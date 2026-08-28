<?php

declare(strict_types=1);

namespace App\Services\Orders\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;

use App\Services\Orders\Infrastructure\OrderTaxRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderTaxRepository::class)]
class OrderTax extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column(type: Types::STRING, nullable: false)]
        private readonly string $taxRatePublicReference,

        #[ORM\Column(type: Types::STRING, nullable: false)]
        private readonly string $taxRateName,

        #[ORM\Column(type: Types::STRING, nullable: false)]
        private readonly string $taxRateHandle,

        #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: "orderTaxes")]
        #[ORM\JoinColumn(nullable: false)]
        private readonly Order $order,

        #[ORM\Column(type: Types::INTEGER, nullable: false)]
        private readonly int $amountCentessimal,
        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getTaxRatePublicReference(): string
    {
        return $this->taxRatePublicReference;
    }

    public function getTaxRateName(): string
    {
        return $this->taxRateName;
    }

    public function getTaxRateHandle(): string
    {
        return $this->taxRateHandle;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getAmountCentessimal(): int
    {
        return $this->amountCentessimal;
    }

    public function getAmountDecimal(): float {
        return $this->getAmountCentessimal()/100;
    }
}
