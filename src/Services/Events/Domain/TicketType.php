<?php

declare(strict_types=1);

namespace App\Services\Events\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Events\Infrastructure\TicketTypeRepository;
use App\Services\Orders\Domain\Product;
use App\Services\User\Domain\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Validator as CustomAssert;
#[ORM\Entity(repositoryClass: TicketTypeRepository::class)]
class TicketType extends AbstractDomainEntity
{
    private const string SKU_PATTERN = "%s.%s.%s";
    public function __construct(
        #[ORM\Column(length: 255)]
        private string $name,

        #[ORM\Column]
        #[CustomAssert\TicketTypeSlugUniqueWithinEventConstraint]
        private readonly string $slug,

        #[ORM\Column(length: 255)]
        private string $description,

        #[ORM\Column(type: 'integer')]
        private int $priceCentessimal,


        #[ORM\OneToOne(targetEntity: Product::class, fetch: 'EAGER')]
        #[ORM\JoinColumn(nullable: false)]
        private Product $product,

        #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'ticketTypes')]
        private readonly Event $event,

        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getSlug(): string
    {
        return $this->slug;
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

    public function getPriceCentessimal(): int
    {
        return $this->priceCentessimal;
    }

    public function setPriceCentessimal(int $priceCentessimal): void
    {
        $this->priceCentessimal = $priceCentessimal;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function getPriceDecimal(): float {
        return $this->priceCentessimal/100;
    }
    public function getProduct(): Product {
        return $this->product;
    }

    public static function generateSku(Event $event, string $ticketTypeSlug): string {
        return strtoupper(sprintf(self::SKU_PATTERN, $event->getOrganisation()->getHandle(), $event->getEventSlug(), $ticketTypeSlug));
    }
}
