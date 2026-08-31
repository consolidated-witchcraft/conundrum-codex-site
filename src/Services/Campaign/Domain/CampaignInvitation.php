<?php

declare(strict_types=1);

namespace App\Services\Campaign\Domain;

use App\Services\Campaign\Infrastructure\CampaignInvitationRepository;
use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CampaignInvitationRepository::class)]
#[UniqueEntity(fields: ['invitationCode'], message: 'There is already an invitation with this code.')]
class CampaignInvitation extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\Column(length: 64, unique: true)]
        private readonly string $invitationCode,
        #[ORM\Column(type: 'email', length: 255)]
        private readonly string $email,
        #[ORM\Column(type: 'non_empty_string', length: 255)]
        private string $invitationMessage,
        #[ORM\ManyToOne(targetEntity: Campaign::class, inversedBy: 'invitations')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private readonly Campaign $campaign,
        #[ORM\Column(type: 'datetime_immutable')]
        private \DateTimeImmutable $expiresAt,
        #[ORM\ManyToOne(targetEntity: User::class)]
        #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
        private ?User $user = null,
        #[ORM\Column(type: 'datetime_immutable', nullable: true)]
        private ?\DateTimeImmutable $usedAt = null,
        ?int $id = null,
    ) {
        parent::__construct(id: $id);
    }

    public function getInvitationCode(): string { return $this->invitationCode; }
    public function getEmail(): string { return $this->email; }
    public function getInvitationMessage(): string { return $this->invitationMessage; }
    public function setInvitationMessage(string $invitationMessage): void { $this->invitationMessage = $invitationMessage; }
    public function getCampaign(): Campaign { return $this->campaign; }
    public function getExpiresAt(): \DateTimeImmutable { return $this->expiresAt; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(User $user): void { $this->user = $user; }
    public function isUsed(): bool { return null !== $this->usedAt; }
    public function markUsed(): void { $this->usedAt = new \DateTimeImmutable(); }
    public function revoke(): void { $this->expiresAt = new \DateTimeImmutable(); }
}
