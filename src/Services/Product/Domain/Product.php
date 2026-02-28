<?php

declare(strict_types=1);

namespace App\Services\Product\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Events\Domain\Organisation;
use App\Services\Product\Infrastructure\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[UniqueEntity(fields: ['baseSku'], message: 'There is already a product with this SKU. Please choose another one.')]
class Product extends AbstractDomainEntity
{
    /**
     * @param string $baseSku
     * @param string $name
     * @param Organisation $organisation
     * @param string $description
     * @param Collection<int, ProductVariant> $productVariants
     * @param int|null $id
     */
    public function __construct(
        #[ORM\Column(type: 'string', length: 255, unique: true)]
        private readonly string $baseSku,

        #[ORM\Column(type: 'string', length: 255)]
        private string $name,

        #[ORM\ManyToOne(targetEntity: Organisation::class)]
        private readonly Organisation $organisation,

        #[ORM\Column(type: 'string', length: 2198)]
        private string $description,

        #[ORM\OneToMany(targetEntity: ProductVariant::class, mappedBy: 'product', cascade: ['persist', 'refresh', 'remove'], orphanRemoval: true)]
        private Collection $productVariants = new ArrayCollection(),

        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getOrganisation(): Organisation
    {
        return $this->organisation;
    }

    public function getBaseSku(): string {
        return $this->baseSku;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getProductVariants(): Collection
    {
        return $this->productVariants;
    }

    public function setProductVariants(Collection $productVariants): void
    {
        $this->productVariants = $productVariants;
        foreach ($productVariants as $productVariant) {
            $productVariant->setProduct($this);
        }
    }
}
