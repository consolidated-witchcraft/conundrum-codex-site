<?php

namespace App\Services\User\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\User\Infrastructure\Repository\UserBlockRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserBlockRepository::class)]
#[ORM\Index(name: 'user_idx', columns: ['user_id'])]
#[ORM\Index(name: 'expr_idx', columns: ['expiration_date'])]
class UserBlock extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\ManyToOne(cascade: ['persist'], fetch: 'EAGER', inversedBy: 'userBlocks')]
        #[ORM\JoinColumn(nullable: false)]
        private readonly User $user,

        #[ORM\Column(length: 255)]
        private string $reason,

        #[ORM\Column(type: 'datetime_immutable', nullable: true)]
        private ?\DateTimeImmutable $expirationDate = null,
        ? int $id = null
    ) {
        parent::__construct($id);
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): void
    {
        $this->reason = $reason;
    }

    public function getExpirationDate(): ?\DateTimeImmutable
    {
        return $this->expirationDate;
    }

    public function setExpirationDate(?\DateTimeImmutable $expirationDate): void
    {
        $this->expirationDate = $expirationDate;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function isPermanent(): bool {
        return $this->expirationDate === null;
    }

    public function isExpired(): bool {
        if ($this->isPermanent()) {
            return false;
        }
        return $this->expirationDate <= new \DateTimeImmutable();

    }
}

