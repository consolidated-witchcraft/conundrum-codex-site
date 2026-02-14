<?php

declare(strict_types=1);

namespace App\Services\Orders\Domain;

use App\Services\Files\Domain\ManagedFile;
use App\Services\Orders\Domain\ValueObjects\OrderChargeType;
use App\Services\Orders\Infrastructure\OrderChargeLineRepository;
use App\Services\Orders\Infrastructure\OrderProductLineRepository;
use App\Services\Orders\Infrastructure\ProductRepository;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Services\Core\Domain\AbstractDomainEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: OrderChargeLineRepository::class)]
class OrderChargeLine extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column(type: 'order_charge_type')]
        private OrderChargeType $chargeType,

        #[ORM\Column(type: 'string')]
        private string $name,

        #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'chargeLines')]
        private readonly Order  $order,

        #[ORM\Column(type: 'integer')]
        private int $priceCentessimal,
        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getChargeType(): OrderChargeType
    {
        return $this->chargeType;
    }

    public function setChargeType(OrderChargeType $chargeType): void
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
