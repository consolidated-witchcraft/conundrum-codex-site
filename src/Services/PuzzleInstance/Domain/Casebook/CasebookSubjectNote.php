<?php

namespace App\Services\PuzzleInstance\Domain\Casebook;

use App\Services\Core\Domain\AbstractDomainEntity;
use App\Services\PuzzleInstance\Infrastructure\Casebook\Repository\CasebookSubjectNoteRepository;
use App\Services\User\Domain\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CasebookSubjectNoteRepository::class)]
class CasebookSubjectNote extends AbstractDomainEntity
{

    public function __construct(
        #[ORM\Column(length: 255)]
        private string $title,

        #[ORM\Column(length: 1024)]
        private string $body,

        #[ORM\ManyToOne]
        #[ORM\JoinColumn(nullable: false)]
        private User $createdBy,

        #[ORM\ManyToOne(inversedBy: 'casebookSubjectNotes')]
        #[ORM\JoinColumn(nullable: false)]
        private CasebookSubject $casebookSubject,
        ?int $id = null
    ) {
        parent::__construct($id);
    }

    public function getCasebookSubject(): CasebookSubject
    {
        return $this->casebookSubject;
    }


    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }
}
