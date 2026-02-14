<?php

declare(strict_types=1);

namespace App\Services\Orders\Domain;

use App\Services\Files\Domain\ManagedFile;
use App\Services\Orders\Domain\ValueObjects\ProductType;
use App\Services\Orders\Infrastructure\ProductRepository;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Services\Core\Domain\AbstractDomainEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[UniqueEntity(fields: ['sku'], message: 'There is already a product with this SKU. Please choose another one.')]
class Product extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column(type: 'string', length: 255, unique: true)]
        private readonly string $sku,

        #[ORM\Column(type: 'string', length: 255)]
        private string $name,

        #[ORM\Column(type: 'product_type', options: ['default' => 'e_ticket'])]
        private ProductType $type,

        #[ORM\Column(type: 'string', length: 2198)]
        private string $description,

        #[ORM\Column(type: 'integer')]
        private int $preTaxPriceCentessimal,

        #[ORM\ManyToOne(targetEntity: User::class)]
        private User $createdBy,

        #[ORM\ManyToOne(targetEntity: ProductStock::class, cascade: ['persist', 'remove', 'refresh'], fetch: 'EAGER', inversedBy: 'products')]
        #[ORM\JoinColumn(nullable: false)]
        private ? ProductStock $productStock = null,

        #[ORM\Column(type: 'boolean')]
        private bool $taxExempt = false,

        #[ORM\Column(type: 'datetime', nullable: true)]
        private ? \DateTimeInterface $publicationDate = null,

        #[ORM\Column(type: 'datetime', nullable: true)]
        private ? \DateTimeInterface $retiredDate = null,

        #[ORM\ManyToMany(targetEntity: ManagedFile::class)]
        private ? Collection $productImages = null,

        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getType(): ProductType
    {
        return $this->type;
    }

    public function setType(ProductType $type): void
    {
        $this->type = $type;
    }

    public function getProductStock(): ProductStock
    {
        return $this->productStock;
    }

    public function setProductStock(ProductStock $productStock): void
    {
        $this->productStock = $productStock;
    }

    public function getSku(): string
    {
        return $this->sku;
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

    public function getProductImages(): ? Collection
    {
        return $this->productImages;
    }

    public function setProductImages(Collection $productImages): void
    {
        $this->productImages = $productImages;
    }

    public function getPreTaxPriceCentessimal(): int
    {
        return $this->preTaxPriceCentessimal;
    }

    public function getPreTaxPriceDecimal(): float {
        return $this->getPreTaxPriceCentessimal()/100;
    }

    public function setPreTaxPriceCentessimal(int $preTaxPriceCentessimal): void
    {
        $this->preTaxPriceCentessimal = $preTaxPriceCentessimal;
    }


    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function isPublished(): bool {
        $now = new \DateTime();
        return $this->publicationDate !== null && $this->publicationDate <= $now;
    }

    public function getPublicationDate(): ?\DateTimeInterface
    {
        return $this->publicationDate;
    }


    public function setPublicationDate(\DateTimeInterface $publicationDate): void {
        $this->publicationDate = $publicationDate;
    }

    public function isRetired(): bool {
        $now = new \DateTime();
        return $this->retiredDate !== null && $this->retiredDate <= $now;
    }

    public function getRetiredDate(): ?\DateTimeInterface {
        return $this->retiredDate;
    }

    public function setRetiredDate(\DateTimeInterface $retiredDate): void {
        $this->retiredDate = $retiredDate;
    }

    public function setTaxExempt(bool $taxExempt): void {
        $this->taxExempt = $taxExempt;
    }

    public function isTaxExempt(): bool {
        return $this->taxExempt;
    }

    public function getFormSafeSku(): string {
        return str_replace('.', ':', $this->getSku());
    }


}
