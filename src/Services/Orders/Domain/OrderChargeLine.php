<?php

declare(strict_types=1);

namespace App\Services\Orders\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Orders\Domain\ValueObjects\ChargeType;
use App\Services\Orders\Infrastructure\OrderChargeLineRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderChargeLineRepository::class)]
class OrderChargeLine extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column(type: 'charge_type')]
        private ChargeType $chargeType,

        #[ORM\Column(type: 'string')]
        private string $name,

        #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'chargeLines')]
        private readonly Order $order,

        #[ORM\Column(type: 'integer')]
        private int $priceCentessimal,
        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getChargeType(): ChargeType
    {
        return $this->chargeType;
    }

    public function setChargeType(ChargeType $chargeType): void
    {
        $this->chargeType = $chargeType;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getPriceCentessimal(): int
    {
        return $this->priceCentessimal;
    }

    public function getPriceDecimal(): float {
        return $this->getPriceCentessimal()/100;
    }

}
