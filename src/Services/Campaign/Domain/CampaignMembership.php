<?php

declare(strict_types=1);

namespace App\Services\Campaign\Domain;

use App\Services\Campaign\Domain\Enums\CampaignMembershipTypeEnum;
use App\Services\Campaign\Domain\Exceptions\CampaignCharacterDoesNotBelongToMembershipException;
use App\Services\Campaign\Domain\Exceptions\DuplicateCampaignCharacterException;
use App\Services\Character\Domain\Character;
use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    /**
     * @param Collection<int, CampaignCharacter> $campaignCharacters
     */
    public function __construct(
        #[ORM\ManyToOne(targetEntity: Campaign::class, inversedBy: 'memberships')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private Campaign $campaign,

        #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'campaignMemberships')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private User $user,

        #[ORM\Column(enumType: CampaignMembershipTypeEnum::class)]
        private CampaignMembershipTypeEnum $type,

        #[ORM\OneToMany(
            targetEntity: CampaignCharacter::class,
            mappedBy: 'campaignMembership',
            cascade: ['persist', 'remove'],
            orphanRemoval: true,
        )]
        private Collection $campaignCharacters = new ArrayCollection(),

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

    /**
     * @return Collection<int, CampaignCharacter>
     */
    public function getCampaignCharacters(): Collection
    {
        return $this->campaignCharacters;
    }

    public function addCharacter(Character $character, bool $active = false): CampaignCharacter
    {
        return new CampaignCharacter(
            campaignMembership: $this,
            character: $character,
            active: $active,
        );
    }

    public function addCampaignCharacter(CampaignCharacter $campaignCharacter): void
    {
        foreach ($this->campaignCharacters as $existingCampaignCharacter) {
            if ($existingCampaignCharacter === $campaignCharacter) {
                return;
            }

            if ($existingCampaignCharacter->getCharacter() === $campaignCharacter->getCharacter()) {
                throw new DuplicateCampaignCharacterException();
            }
        }

        $this->campaignCharacters->add($campaignCharacter);
    }

    public function removeCampaignCharacter(CampaignCharacter $campaignCharacter): void
    {
        if ($campaignCharacter->getCampaignMembership() !== $this) {
            throw new CampaignCharacterDoesNotBelongToMembershipException();
        }

        if ($this->campaignCharacters->removeElement($campaignCharacter)) {
            $campaignCharacter->getCharacter()->removeCampaignCharacter(campaignCharacter: $campaignCharacter);
        }
    }

    public function activateCharacter(CampaignCharacter $campaignCharacter): void
    {
        if (
            $campaignCharacter->getCampaignMembership() !== $this
            || !$this->campaignCharacters->contains($campaignCharacter)
        ) {
            throw new CampaignCharacterDoesNotBelongToMembershipException();
        }

        foreach ($this->campaignCharacters as $existingCampaignCharacter) {
            $existingCampaignCharacter->markInactive();
        }

        $campaignCharacter->markActive();
    }

}
