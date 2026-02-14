<?php

declare(strict_types=1);

namespace App\Services\Ticket\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\Ticket\Infrastructure\TicketRepository;
use App\Services\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[UniqueEntity(fields: 'ticketCode', message: 'A ticket with this code already exists')]
class Ticket extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column(type: 'email')]
        private readonly string $customerEmail,

        #[ORM\Column(type: 'string')]
        private readonly string $customerName,

        #[ORM\Column(type: 'string', unique: true)]
        private readonly string $ticketCode,

        #[ORM\ManyToOne(targetEntity: TicketType::class, fetch: 'EAGER')]
        private readonly TicketType $ticketType,

        #[ORM\Column(type: 'datetime_immutable')]
        private readonly \DateTimeImmutable $issuedDate,

        #[ORM\Column(type: 'datetime_immutable')]
        private \DateTimeImmutable $paidDate,

        #[ORM\ManyToOne(targetEntity: User::class)]
        #[ORM\JoinColumn(nullable: true)]
        private readonly ? User $user = null,

        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getIssuedDate(): \DateTimeImmutable
    {
        return $this->issuedDate;
    }

    public function getPaidDate(): \DateTimeImmutable
    {
        return $this->paidDate;
    }

    public function getCustomerEmail(): string {
        return $this->customerEmail;
    }

    public function getCustomerName(): string {
        return $this->customerName;
    }

    public function getTicketCode(): string {
        return $this->ticketCode;
    }

    public function getTicketType(): TicketType {
        return $this->ticketType;
    }

    public function getUser(): ? User {
        return $this->user;
    }

    public function markPaid(): void {
        $this->paidDate = new \DateTimeImmutable();
    }
}
