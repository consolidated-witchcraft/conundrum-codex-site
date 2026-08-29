<?php

declare(strict_types=1);

namespace App\Services\Character\Domain;

use App\Services\Core\Domain\AbstractDomainEntity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class CharacterNote extends AbstractDomainEntity
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: Character::class, inversedBy: 'characterNotes')]
        #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
        private Character $character,

        #[ORM\Column(type: 'non_empty_string', length: 255)]
        private string $subject,

        #[ORM\Column(type: Types::TEXT)]
        private string $body,

        ?int $id = null,
    ) {
        parent::__construct(id: $id);
    }

    public function getCharacter(): Character
    {
        return $this->character;
    }

    public function setCharacter(Character $character): void
    {
        $this->character = $character;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

}
