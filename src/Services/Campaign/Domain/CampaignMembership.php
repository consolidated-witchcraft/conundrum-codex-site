<?php

declare(strict_types=1);

namespace App\Services\Campaign\Domain;

use App\Services\Campaign\Domain\Enums\CampaignMembershipTypeEnum;
use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[ORM\UniqueConstraint(
    name: 'campaign_membership_campaign_user_unique',
    columns: ['campaign_id', 'user_id'],
)]
#[UniqueEntity(fields: ['campaign', 'user'])]
class CampaignMembership extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: Campaign::class, inversedBy: 'memberships')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private Campaign $campaign,

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'campaignMemberships')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private User $user,

        #[ORM\Column(enumType: CampaignMembershipTypeEnum::class)]
        private CampaignMembershipTypeEnum $type,

        #[ORM\OneToOne(
            targetEntity: Character::class,
            mappedBy: 'campaignMembership',
            cascade: ['persist', 'remove'],
            orphanRemoval: true,
        )]
        private ?Character $character = null,

        ?int $id = null,
    ) {
        parent::__construct(id: $id);
    }

    public function getCampaign(): Campaign
    {
        return $this->campaign;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getType(): CampaignMembershipTypeEnum
    {
        return $this->type;
    }

    public function setType(CampaignMembershipTypeEnum $type): void
    {
        $this->type = $type;
    }

    public function getCharacter(): ?Character
    {
        return $this->character;
    }

    public function setCharacter(Character $character): void
    {
        $this->character = $character;
    }

    public function removeCharacter(): void
    {
        $this->character = null;
    }
}
