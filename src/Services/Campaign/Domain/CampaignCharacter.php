<?php

declare(strict_types=1);

namespace App\Services\Campaign\Domain;

use App\Services\Campaign\Domain\Exceptions\CharacterOwnerDoesNotMatchCampaignMembershipException;
use App\Services\Character\Domain\Character;
use App\Services\Core\Domain\AbstractDomainEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[ORM\UniqueConstraint(
    name: 'campaign_character_membership_character_unique',
    columns: ['campaign_membership_id', 'character_id'],
)]
#[ORM\UniqueConstraint(
    name: 'campaign_character_active_membership_unique',
    columns: ['campaign_membership_id'],
    options: ['where' => '(active = true)'],
)]
#[UniqueEntity(fields: ['campaignMembership', 'character'])]
class CampaignCharacter extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: CampaignMembership::class, inversedBy: 'campaignCharacters')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private CampaignMembership $campaignMembership,

        #[ORM\ManyToOne(targetEntity: Character::class, inversedBy: 'campaignCharacters')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private Character $character,

        #[ORM\Column(options: ['default' => false])]
        private bool $active = false,

        ?int $id = null,
    ) {
        if ($character->getUser() !== $campaignMembership->getUser()) {
            throw new CharacterOwnerDoesNotMatchCampaignMembershipException();
        }

        parent::__construct(id: $id);

        $campaignMembership->addCampaignCharacter(campaignCharacter: $this);
        $character->addCampaignCharacter(campaignCharacter: $this);

        if ($active) {
            $campaignMembership->activateCharacter(campaignCharacter: $this);
        }
    }

    public function getCampaignMembership(): CampaignMembership
    {
        return $this->campaignMembership;
    }

    public function getCharacter(): Character
    {
        return $this->character;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function markActive(): void
    {
        $this->active = true;
    }

    public function markInactive(): void
    {
        $this->active = false;
    }
}
