<?php

declare(strict_types=1);

namespace App\Services\Events\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Events\Infrastructure\TicketTypeRepository;
use App\Services\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketTypeRepository::class)]
class TicketType extends AbstractDomainEntity
{

    public function __construct(
        #[ORM\Column(length: 255)]
        private string $name,

        #[ORM\Column(length: 255)]
        private string $description,

        #[ORM\Column]
        private int $priceCentessimal,

        #[ORM\Column(type: 'datetime')]
        private \DateTimeInterface $startDate,

        #[ORM\Column(type: 'datetime')]
        private \DateTimeInterface $endDate,

        #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'ticketTypes')]
        private Event $event,

        ? int $id = null
    ) {
        parent::__construct($id);
    }

}
