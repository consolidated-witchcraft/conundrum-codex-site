<?php

declare(strict_types=1);

namespace App\Services\User\Domain\UserCart;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Products\Domain\Product;
use App\Services\User\Domain\User;
use App\Services\User\Infrastructure\UserCart\UserCartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserCartRepository::class)]
class UserCart extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'userCart', cascade: ['persist', 'refresh'], orphanRemoval: true)]
        private User $user,

        #[ORM\OneToMany(targetEntity: UserCartLine::class, mappedBy: 'userCart', cascade: ['persist', 'refresh'], fetch: 'EAGER', orphanRemoval: true)]
        private Collection $userCartLines = new ArrayCollection(),
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

    public function getUserCartLines(): Collection
    {
        return $this->userCartLines;
    }

    public function getUserCartLineBySku(string $sku): ?UserCartLine {

        foreach ($this->userCartLines as $userCartLine) {

            if ($userCartLine->getProduct()->getSku() === $sku) {
                return $userCartLine;
            }
        }
        return null;
    }

    public function addCartLine(Product $product, int $quantity): void {
        $existingLine = $this->getUserCartLineBySku($product->getSku());
        if ($existingLine !== null) {
            $currentQuantity = $existingLine->getQuantity();
            $newQuantity = $currentQuantity + $quantity;
            $existingLine->setQuantity($newQuantity);
        } else {
            $this->userCartLines->add(new UserCartLine(
                userCart: $this,
                productVariant: $product,
                quantity: $quantity
            ));
        }
    }

    public function clearCart(): void {
        $this->userCartLines->clear();
    }
}
