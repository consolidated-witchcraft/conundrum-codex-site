<?php

declare(strict_types=1);

namespace App\Services\User\Domain\UserCart;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Orders\Domain\ValueObjects\ChargeType;
use App\Services\Product\Domain\ProductVariant;
use App\Services\User\Domain\User;
use App\Services\User\Infrastructure\UserCart\UserCartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserCartRepository::class)]
class UserCart extends AbstractDomainEntity
{
    /**
     * @param User $user
     * @param Collection<int, UserCartProductLine> $userCartProductLines
     * @param Collection<int, UserCartCharge> $userCartCharges
     * @param int|null $id
     */
    public function __construct(
        #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'userCart', cascade: ['persist', 'refresh'], orphanRemoval: true)]
        private User                $user,

        #[ORM\OneToMany(targetEntity: UserCartProductLine::class, mappedBy: 'userCart', cascade: ['persist', 'refresh'], fetch: 'EAGER', orphanRemoval: true)]
        private readonly Collection $userCartProductLines = new ArrayCollection(),

        #[ORM\OneToMany(targetEntity: UserCartCharge::class, mappedBy: 'userCart', cascade: ['persist', 'refresh'], fetch: 'EAGER', orphanRemoval: true)]
        private Collection $userCartCharges = new ArrayCollection(),


        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return Collection<int, UserCartProductLine>
     */
    public function getUserCartProductLines(): Collection
    {
        return $this->userCartProductLines;
    }

    public function getUserCartLineBySku(string $sku): ?UserCartProductLine {

        foreach ($this->userCartProductLines as $userCartLine) {

            if ($userCartLine->getProductVariant()->getSku() === $sku) {
                return $userCartLine;
            }
        }
        return null;
    }

    /**
     * @return Collection<int, UserCartCharge>
     */
    public function getUserCartChargeLines(): Collection {
        return $this->userCartCharges;
    }

    public function addProductCartLine(ProductVariant $product, int $quantity): void {
        $existingLine = $this->getUserCartLineBySku($product->getSku());

        if ($existingLine !== null) {
            $currentQuantity = $existingLine->getQuantity();
            $newQuantity = $currentQuantity + $quantity;
            $existingLine->setQuantity($newQuantity);
        } else {
            if ($quantity > 0) {
                $this->userCartProductLines->add(new UserCartProductLine(
                    userCart: $this,
                    productVariant: $product,
                    quantity: $quantity
                ));
            }

        }
        $this->generateChargeLines();
    }

    public function clearCart(): void {
        $this->userCartProductLines->clear();
    }

    public function getPretaxTotalCentessimal(): int {
        $total = 0;
        foreach ($this->userCartProductLines as $userCartLine) {
            $total += $userCartLine->getPretaxTotalCentessimal();
        }

        foreach ($this->userCartCharges as $userCartCharge) {
            $total += $userCartCharge->getPretaxAmountCentessimal();
        }

        return $total;
    }

    public function getPretaxTotalDecimal(): float {
        return $this->getPretaxTotalCentessimal()/100;
    }

    public function generateChargeLines(): void {
        $shouldGeneratePostageCharge = false;
        $shouldGenerateServiceCharge = false;
        foreach ($this->userCartProductLines as $userCartProductLine) {
            if ($userCartProductLine->getProductVariant()->requiresPostage()) {
                $shouldGeneratePostageCharge = true;
            }
            if ($userCartProductLine->getProductVariant()->getType()->getHandle() === 'e_ticket') {
                $shouldGenerateServiceCharge = true;
            }
        }
        $this->userCartCharges->clear();
        if ($shouldGeneratePostageCharge) {
            $this->userCartCharges->add(new UserCartCharge(
                userCart: $this,
                chargeType: ChargeType::makeFromHandle('delivery_charge'),
                preTaxAmountCentessimal: 200
            ));
        }
        if ($shouldGenerateServiceCharge) {
            $this->userCartCharges->add(new UserCartCharge(
                userCart: $this,
                chargeType: ChargeType::makeFromHandle('service_charge'),
                preTaxAmountCentessimal: 100
            ));
        }
    }

    public function getUserCartProductLineById(int $productLineId): ?UserCartProductLine {
        foreach ($this->userCartProductLines as $userCartProductLine) {

            if ($userCartProductLine->getId() === $productLineId) {
                return $userCartProductLine;
            }
        }
        return null;
    }

    public function removeUserCartProductLine(UserCartProductLine $userCartProductLine): void {
        $this->userCartProductLines->removeElement($userCartProductLine);
    }
}
