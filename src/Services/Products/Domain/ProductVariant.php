<?php

declare(strict_types=1);

namespace App\Services\Products\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Files\Domain\ManagedFile;
use App\Services\Products\Domain\ValueObjects\ProductType;
use App\Services\Products\Infrastructure\ProductVariantRepository;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ProductVariantRepository::class)]
#[UniqueEntity(fields: ['sku'], message: 'There is already a productvariant  with this SKU. Please choose another one.')]
class ProductVariant extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column(type: 'string', length: 255, unique: true)]
        private readonly string $sku,

        #[ORM\Column(type: 'product_type', options: ['default' => 'e_ticket'])]
        private readonly ProductType $type,

        #[ORM\Column(type: 'integer')]
        private int $delta,

        #[ORM\Column(type: 'integer')]
        private int $preTaxPriceCentessimal,

        #[ORM\ManyToOne(targetEntity: User::class)]
        private readonly User $createdBy,

        #[ORM\Column(type: 'boolean')]
        private bool $taxExempt = false,

        #[ORM\Column(type: 'boolean')]
        private bool $requiresPostage = false,

        #[ORM\Column(type: 'datetime', nullable: true)]
        private \DateTimeInterface $publicationDate,

        #[ORM\OneToMany(targetEntity: ManagedFile::class)]
        private Collection $productVariantImages = new ArrayCollection(),

        #[ORM\Column(type: 'datetime', nullable: true)]
        private ? \DateTimeInterface $retiredDate = null,

        #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'productVariants')]
        #[ORM\JoinColumn(nullable: false)]
        private ? Product $product = null,

        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getSku(): string {
        return $this->sku;
    }

    public function getType(): ProductType {
        return $this->type;
    }

    public function setDelta(int $delta): void {
        $this->delta = $delta;
    }

    public function getDelta(): int
    {
        return $this->delta;
    }

    public function setPreTaxPriceCentessimal(int $preTaxPriceCentessimal): void {
        $this->preTaxPriceCentessimal = $preTaxPriceCentessimal;
    }

    public function getPreTaxPriceCentessimal(): int {
        return $this->preTaxPriceCentessimal;
    }

    public function getPreTaxPriceDecimal(): float {
        return $this->getPreTaxPriceCentessimal()/100;
    }

    public function getCreatedBy(): User {
        return $this->createdBy;
    }

    public function setTaxExempt(bool $taxExempt): void {
        $this->taxExempt = $taxExempt;
    }

    public function isTaxExempt(): bool {
        return $this->taxExempt;
    }

    public function setRequiresPostage(bool $requiresPostage): void {
        $this->requiresPostage = $requiresPostage;
    }

    public function requiresPostage(): bool {
        return $this->requiresPostage;
    }

    public function setPublicationDate(\DateTimeInterface $publicationDate): void {
        $this->publicationDate = $publicationDate;
    }


    public function getPublicationDate(): \DateTimeInterface {
        return $this->publicationDate;
    }

    public function isPublished(): bool {
        $date= new \DateTime();
        return $this->publicationDate <= $date;
    }

    public function setRetiredDate(\DateTimeInterface $retiredDate): void {
        $this->retiredDate = $retiredDate;
    }

    public function getRetiredDate(): ?\DateTimeInterface {
        return $this->retiredDate;
    }

    public function isRetired(): bool {
        $date= new \DateTime();
        return $this->retiredDate <= $date;
    }

    public function setProductVariantImages(Collection $productVariantImages): void {
        $this->productVariantImages = $productVariantImages;
    }

    public function getProductVariantImages(): Collection {
        return $this->productVariantImages;
    }

    public function addProductVariantImage(ManagedFile $productVariantImage): void {
        $this->productVariantImages->add($productVariantImage);
    }

    public function removeProductVariantImage(ManagedFile $productVariantImage): void {
        if ($this->productVariantImages->contains($productVariantImage)) {
            $this->productVariantImages->removeElement($productVariantImage);
        }
    }

    public function setProduct(Product $product): void {
        $this->product = $product;
    }

    public function getProduct(): ?Product {
        return $this->product;
    }
}
