<?php

declare(strict_types=1);

namespace App\Services\PlayEvent\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\PlayEvent\Domain\Enums\PlayEventTypeEnum;
use App\Services\PlayEvent\Infrastructure\PlayEventRepository;
use App\Services\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PlayEventRepository::class)]
class PlayEvent extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: User::class)]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private User $user,

        #[ORM\Column(enumType: PlayEventTypeEnum::class)]
        private PlayEventTypeEnum $type,

        #[ORM\Column(type: 'non_empty_string', length: 255)]
        private string $puzzleInstanceCode,

        #[ORM\Column(type: 'non_empty_string', length: 255)]
        private string $puzzleTemplateSlug,

        ?int $id = null
    ) {
        parent::__construct($id);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function getType(): PlayEventTypeEnum
    {
        return $this->type;
    }

    public function setType(PlayEventTypeEnum $type): void
    {
        $this->type = $type;
    }

    public function getPuzzleInstanceCode(): string
    {
        return $this->puzzleInstanceCode;
    }

    public function setPuzzleInstanceCode(string $puzzleInstanceCode): void
    {
        $this->puzzleInstanceCode = $puzzleInstanceCode;
    }

    public function getPuzzleTemplateSlug(): string
    {
        return $this->puzzleTemplateSlug;
    }

    public function setPuzzleTemplateSlug(string $puzzleTemplateSlug): void
    {
        $this->puzzleTemplateSlug = $puzzleTemplateSlug;
    }
}
