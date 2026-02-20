<?php

namespace App\Services\User\Domain\UserCart;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Orders\Domain\ValueObjects\ChargeType;

use App\Services\User\Infrastructure\UserCart\UserCartChargeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserCartChargeRepository::class)]
class UserCartCharge extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: UserCart::class, inversedBy: 'userCartCharges')]
        private readonly UserCart $userCart,

        #[ORM\Column(type: 'charge_type')]
        private readonly ChargeType $chargeType,

        #[ORM\Column]
        private readonly int $preTaxAmountCentessimal,
        ?int $id = null
    ) {
        parent::__construct($id);
    }

    public function getChargeType(): ChargeType
    {
        return $this->chargeType;
    }

    public function getPretaxAmountCentessimal(): int
    {
        return $this->preTaxAmountCentessimal;
    }

    public function getPreTaxAmountDecimal(): float
    {
        return $this->getPretaxAmountCentessimal()/100;
    }

    public function getUserCart(): UserCart {
        return $this->userCart;
    }
}
